<?php

namespace App\Http\Controllers;

use App\Http\Resources\GroupResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\GroupUserResource;
use App\Http\Resources\PostAttachmentResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Enums\GroupUserStatus;
use App\Http\Enums\GroupUserRole;
use App\Http\Requests\InviteUsersRequest;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\User;
use App\Notifications\InvitationApproved;
use App\Notifications\InvitationInGroup;
use App\Notifications\RequestApproved;
use App\Notifications\RequestToJoinGroup;
use App\Notifications\RoleChanged;
use App\Notifications\UserRemovedFromGroup;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Inertia\Inertia;

class GroupController extends Controller
{
    public function profile(Request $request, Group $group)
    {
        $group->load('currentUserGroup');

        $userId = Auth::id();

        if ($group->hasApprovedUser($userId)) {
            $posts = Post::postsForTimeline($userId, false)
                ->leftJoin('groups AS g', 'g.pinned_post_id', 'posts.id')
                ->where('group_id', $group->id)
                ->orderBy('g.pinned_post_id', 'desc')
                ->orderBy('posts.created_at', 'desc')
                ->paginate(10);
            $posts = PostResource::collection($posts);
        } else {
            return Inertia::render('Group/View', [
                'success' => session('success'),
                'group' => new GroupResource($group),
                'posts' => null,
                'users' => [],
                'requests' => []
            ]);
        }

        if ($request->wantsJson()) {
            return $posts;
        }

        $users = User::query()
            ->select(['users.*', 'gu.role', 'gu.status', 'gu.group_id'])
            ->join('group_users AS gu', 'gu.user_id', 'users.id')
            ->orderBy('users.name')
            ->where('group_id', $group->id)
            ->get();
        $requests = $group->pendingUsers()->orderBy('name')->get();

        $photos = PostAttachment::query()
            ->select('post_attachments.*')
            ->join('posts AS p', 'p.id', 'post_attachments.post_id')
            ->where('p.group_id', $group->id)
            ->where('mime', 'like', 'image/%')
            ->latest()
            ->get();


        return Inertia::render('Group/View', [
            'success' => session('success'),
            'group' => new GroupResource($group),
            'posts' => $posts,
            'users' => GroupUserResource::collection($users),
            'requests' => UserResource::collection($requests),
            'photos' => PostAttachmentResource::collection($photos)
        ]);
    }

    public function store(StoreGroupRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $group = Group::create($data);

        $groupUserData = [
            'status' => GroupUserStatus::APPROVED->value,
            'role' => GroupUserRole::ADMIN->value,
            'user_id' => Auth::id(),
            'group_id' => $group->id,
            'created_by' => Auth::id()
        ];

        GroupUser::create($groupUserData);
        $group->status = $groupUserData['status'];
        $group->role = $groupUserData['role'];

        return response(new GroupResource($group), 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {
        $group->update($request->validated());

        return back()->with('success', "Group was updated");
    }

    public function destroy(Group $group)
    {
        //
    }

    public function updateImage(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::id())) {
            return response("You don't have permission to perform this action", 403);
        }
        $data = $request->validate([
            'cover' => ['nullable', 'image'],
            'thumbnail' => ['nullable', 'image']
        ]);
        $thumbnail = $data['thumbnail'] ?? null;

        /** @var \Illuminate\Http\UploadedFile $cover */
        $cover = $data['cover'] ?? null;

        $success = '';

        if ($cover) {
            if ($group->cover_path) {
                Storage::disk('public')->delete($group->cover_path);
            }
            $path = $cover->store('group-' . $group->id, 'public');
            $group->update(['cover_path' => $path]);
            $success = 'Your cover image was updated';
        }

        if ($thumbnail) {
            if ($group->thumbnail_path) {
                Storage::disk('public')->delete($group->thumbnail_path);
            }
            $path = $thumbnail->store('group-' . $group->id, 'public');
            $group->update(['thumbnail_path' => $path]);
            $success = 'Your thumbnail image was updated';
        }

        //        session('success', 'Cover image has been updated');

        return back()->with('success', $success);
    }
    //cambiar
    public function inviteUsers(InviteUsersRequest $request, Group $group)
    {
        $data = $request->validated();
        $inviter = Auth::user();
        $user = $request->user;

        // Verificar si el usuario ya está en el grupo con estado diferente a PENDING
        $existingGroupUser = GroupUser::where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->where('status', '!=', GroupUserStatus::PENDING->value)
            ->first();

        if ($existingGroupUser) {
            return back()->with('error', 'El usuario ya es miembro de este grupo');
        }

        // Eliminar invitación previa si existe
        GroupUser::where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->where('status', GroupUserStatus::PENDING->value)
            ->delete();

        // Crear nueva invitación
        $hours = 24;
        $token = Str::random(64); // Longitud más segura

        try {
            $groupUser = GroupUser::create([
                'status' => GroupUserStatus::PENDING->value,
                'role' => GroupUserRole::USER->value,
                'token' => hash('sha256', $token), // Almacenar hash en lugar del token plano
                'token_expire_date' => Carbon::now()->addHours($hours),
                'user_id' => $user->id,
                'group_id' => $group->id,
                'created_by' => $inviter->id,
            ]);

            // Enviar notificación con el token original (no el hash)
            $user->notify(new InvitationInGroup($group, $hours, $token));

            return back()->with('success', 'Invitación enviada correctamente');
        } catch (\Exception $e) {
            Log::error("Error al invitar usuario al grupo: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al enviar la invitación');
        }
    }

    public function approveInvitation(string $token)
    {
        // Buscar la invitación con relaciones precargadas para mejor performance
        $groupUser = GroupUser::with(['group', 'user', 'adminUser'])
            ->where('token', hash('sha256', $token)) // Buscar por hash del token
            ->first();

        // Validaciones mejoradas
        if (!$groupUser) {
            return $this->handleInvitationError('The invitation link is not valid');
        }

        if ($groupUser->token_used || $groupUser->status === GroupUserStatus::APPROVED->value) {
            return $this->handleInvitationError('This invitation has already been used');
        }

        if ($groupUser->token_expire_date < now()) {
            return $this->handleInvitationError('The invitation link has expired');
        }

        // Procesar la aceptación en una transacción
        try {
            DB::transaction(function () use ($groupUser) {
                $groupUser->update([
                    'status' => GroupUserStatus::APPROVED->value,
                    'token_used' => now(),
                    'approved_at' => now(), // Campo adicional para tracking
                ]);

                // Notificar al admin/creador del grupo
                $groupUser->adminUser->notify(
                    new InvitationApproved($groupUser->group, $groupUser->user)
                );
            });

            return redirect()
                ->route('group.profile', $groupUser->group)
                ->with('success', __(
                    'You have successfully joined the group ":group"',
                    ['group' => $groupUser->group->name]
                ));
        } catch (\Exception $e) {
            Log::error("Error approving group invitation: " . $e->getMessage());
            return $this->handleInvitationError('An error occurred while processing your request');
        }
    }

    protected function handleInvitationError(string $message)
    {
        return inertia('Error', [
            'errorTitle' => $message,
            'errorDescription' => 'Please contact the group administrator for a new invitation.',
        ]);
    }

    public function join(Group $group)
    {
        $user = \request()->user();

        $status = GroupUserStatus::APPROVED->value;
        $successMessage = 'You have joined to group "' . $group->name . '"';
        if (!$group->auto_approval) {
            $status = GroupUserStatus::PENDING->value;

            Notification::send($group->adminUsers, new RequestToJoinGroup($group, $user));
            $successMessage = 'Your request has been accepted. You will be notified once you will be approved';
        }

        GroupUser::create([
            'status' => $status,
            'role' => GroupUserRole::USER->value,
            'user_id' => $user->id,
            'group_id' => $group->id,
            'created_by' => $user->id,
        ]);

        return back()->with('success', $successMessage);
    }

    public function approveRequest(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::id())) {
            return response("No tienes permiso para realizar esta acción", 403);
        }

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'action' => ['required', 'in:approve,reject']
        ]);

        $groupUser = GroupUser::where('user_id', $data['user_id'])
            ->where('group_id', $group->id)
            ->where('status', GroupUserStatus::PENDING->value)
            ->firstOrFail();

        $approved = $data['action'] === 'approve';
        $groupUser->status = $approved
            ? GroupUserStatus::APPROVED->value
            : GroupUserStatus::REJECTED->value;

        $groupUser->save();

        $user = $groupUser->user;
        $user->notify(new RequestApproved(
            $group,
            $user,
            $approved,
            Auth::user() // Quién procesó la solicitud
        ));

        return back()->with(
            'success',
            'Solicitud de "' . $user->name . '" ' .
                ($approved ? 'aprobada' : 'rechazada') .
                ' correctamente'
        );
    }
    public function removeUser(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::id())) {
            return response("You don't have permission to perform this action", 403);
        }

        $data = $request->validate([
            'user_id' => ['required'],
        ]);

        $user_id = $data['user_id'];
        if ($group->isOwner($user_id)) {
            return response("The owner of the group cannot be removed", 403);
        }

        $groupUser = GroupUser::where('user_id', $user_id)
            ->where('group_id', $group->id)
            ->first();

        if ($groupUser) {
            $user = $groupUser->user;
            $groupUser->delete();

            $user->notify(new UserRemovedFromGroup($group));
        }

        return back();
    }

    public function changeRole(Request $request, Group $group)
    {
        if (!$group->isAdmin(Auth::id())) {
            return response("You don't have permission to perform this action", 403);
        }

        $data = $request->validate([
            'user_id' => ['required'],
            'role' => ['required', Rule::enum(GroupUserRole::class)]
        ]);

        $user_id = $data['user_id'];
        if ($group->isOwner($user_id)) {
            return response("You can't change role of the owner of the group", 403);
        }

        $groupUser = GroupUser::where('user_id', $user_id)
            ->where('group_id', $group->id)
            ->first();

        if ($groupUser) {
            $groupUser->role = $data['role'];
            $groupUser->save();

            $groupUser->user->notify(new RoleChanged($group, $data['role']));
        }

        return back();
    }
}

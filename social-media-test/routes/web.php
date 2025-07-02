<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;

Route::get('/', [HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/u/{user:username}', [ProfileController::class, 'index'])
    ->name('profile');

Route::get('/g/{group:slug}', [GroupController::class, 'profile'])
    ->name('group.profile');

Route::get('/group/approve-invitation/{token}', [GroupController::class, 'approveInvitation'])
    ->name('group.approveInvitation');


Route::middleware('auth')->group(function () {

    // Groups
    Route::prefix('/group')->group(function () {

        Route::post('/', [GroupController::class, 'store'])
            ->name('group.create');

        Route::put('/{group:slug}', [GroupController::class, 'update'])
            ->name('group.update');

        Route::post('/update-images/{group:slug}', [GroupController::class, 'updateImage'])
            ->name('group.updateImages');

        Route::post('/invite/{group:slug}', [GroupController::class, 'inviteUsers'])
            ->name('group.inviteUsers');

        Route::post('/join/{group:slug}', [GroupController::class, 'join'])
            ->name('group.join');

        Route::post('/approve-request/{group:slug}', [GroupController::class, 'approveRequest'])
            ->name('group.approveRequest');

        Route::delete('/remove-user/{group:slug}', [GroupController::class, 'removeUser'])
            ->name('group.removeUser');

        Route::post('/change-role/{group:slug}', [GroupController::class, 'changeRole'])
            ->name('group.changeRole');

        Route::post('/groups/{group}/leave', [GroupController::class, 'leaveGroup'])
            ->name('group.leave');
    });

    // Posts
    Route::prefix('/post')->group(function () {
        Route::get('/{post}', [PostController::class, 'view'])
            ->name('post.view');

        Route::get('/{post}/{id}', [PostController::class, 'viewIndividual'])
            ->name('post.viewIndividual');

        Route::post('', [PostController::class, 'store'])
            ->name('post.create');

        Route::put('/{post}', [PostController::class, 'update'])
            ->name('post.update');

        Route::delete('/{post}', [PostController::class, 'destroy'])
            ->name('post.destroy');

        Route::get('/download/{attachment}', [PostController::class, 'downloadAttachment'])
            ->name('post.download');

        Route::post('/{post}/reaction', [PostController::class, 'postReaction'])
            ->middleware('throttle:60,1') // 60 intentos por minuto
            ->name('post.reaction');

        Route::post('/{post}/comment', [PostController::class, 'createComment'])
            ->name('post.comment.create');

        Route::post('/ai-post', [PostController::class, 'aiPostContent'])
            ->name('post.aiContent');

        Route::post('/fetch-url-preview', [PostController::class, 'fetchUrlPreview'])
            ->name('post.fetchUrlPreview');

        Route::post('/{post}/pin', [PostController::class, 'pinUnpin'])
            ->name('post.pinUnpin');
    });

    // Comments
    Route::delete('/comment/{comment}', [PostController::class, 'deleteComment'])
        ->name('comment.delete');

    Route::put('/comment/{comment}', [PostController::class, 'updateComment'])
        ->name('comment.update');

    Route::post('/comment/{comment}/reaction', [PostController::class, 'commentReaction'])
        ->name('comment.reaction');

    //Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/update-images', [ProfileController::class, 'updateImage'])
        ->name('profile.updateImages');
    Route::post('/user/follow/{user}', [UserController::class, 'follow'])->name('user.follow');

    //notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::post('/notifications/clear', [NotificationController::class, 'clearNotifications'])->name('notifications.clear');

    Route::get('/search/{search?}', [SearchController::class, 'search'])
        ->name('search');

    //Stories
    Route::get('/stories', [StoryController::class, 'index'])->name('stories');
    Route::post('/stories', [StoryController::class, 'store'])->name('stories.store');
    Route::post('/stories/view', [StoryController::class, 'markAsViewed'])->name('stories.view');
    Route::delete('/stories/{story}', [StoryController::class, 'destroy'])->name('stories.destroy');

    //messages
    Route::get('/messages', [MessageController::class, 'index'])->name('messages');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{user}', [MessageController::class, 'chat'])->name('messages.chat');


});

require __DIR__ . '/auth.php';

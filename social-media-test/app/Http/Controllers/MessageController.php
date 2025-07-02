<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class MessageController extends Controller
{
    public function index()
    {
        // Aquí puedes pasar mensajes reales si los tienes
        $messages = [
            [
                'from' => 'Ana Gómez',
                'subject' => 'Cotización de tienda virtual',
                'preview' => 'Hola, estoy interesada en una tienda personalizada...',
                'date' => '2025-07-01',
            ],
            [
                'from' => 'Carlos Díaz',
                'subject' => 'Consulta sobre pasarela de pago',
                'preview' => 'Tengo dudas sobre la integración con Stripe...',
                'date' => '2025-06-30',
            ],
        ];

        return Inertia::render('Messages/Index', [
            'messages' => $messages
        ]);
    }
}

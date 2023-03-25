<?php

namespace App\Http\Controllers;

use BeyondCode\LaravelWebSockets\WebSockets\Channels\Channel;

use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function index(Request $request)
    {
        // $channel = new Channel('chat');
        // $channel->broadcast(['message' => 'Hola, mundo']);
        // $post           = file_get_contents('php://input');
        // $secret         = env('ADAMSPAY_API_SECRET');
        // $hmacExpected   = md5('adams' . $post . $secret);
        // $hmacRecived    = $request->header('x-adams-notify-hash');

        // if ($hmacExpected !== $hmacRecived) return response()->json('', 401);
    }
}

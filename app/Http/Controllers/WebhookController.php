<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function index(Request $request)
    {
        $post           = file_get_contents('php://input');
        $secret         = env('ADAMSPAY_API_SECRET');
        $hmacExpected   = md5('adams' . $post . $secret);
        $hmacRecived    = $request->header('x-adams-notify-hash');
        $request = \json_decode($request->getContent(), true);

        if ($hmacExpected !== $hmacRecived) return response()->json([], 401);

        if ($request['notify']['type'] === 'debtStatus') $this->updateStatus($request);

        return response()->json([], 204);
    }

    public function updateStatus($request)
    {
        $docId              = $request['debt']['docId'];
        $payment            = Payment::where('doc_id', $docId)->first();
        $payment->status    = $request['debt']['payStatus']['status'];
        $payment->save();
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDebtRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function createDebt(CreateDebtRequest $request)
    {
        $url = 'https://staging.adamspay.com/api/v1/debts';
        $apiKey = env('API_KEY');
        $docId  = Str::uuid();
        $start  = Carbon::now()->toAtomString();
        $end    = Carbon::now()->addDays(7)->toAtomString();

        $data = [
            'debt' => [
                'docId'         => $docId,
                'amount'        => [
                    'currency'  => 'PYG',
                    'value'     => (string) $request->value,
                ],
                'label'         => $request->description,
                'target'        => [
                    'type'      => 'cip',
                    'number'    => (string) $request->cip,
                    'label'     => $request->name,
                ],
                'validPeriod'   => [
                    'start'     => $start,
                    'end'       => $end,
                ],
            ]
        ];

        $headers = [
            'apikey'        => $apiKey,
            'Content-Type'  => 'application/json'
        ];

        $response           = Http::withHeaders($headers)->post($url, $data);
        $responseBody       = json_decode($response->body());
        $statusCodeFromAdam = $response->status();

        if ($statusCodeFromAdam === 201) {
            $message = 'Deuda creada exitosamente';
        } else {
            $message = 'Hubo un error al generar la deuda';
        }

        return response()->json([
            'message' => $message,
            'respnse' => $responseBody,
        ]);
    }
}

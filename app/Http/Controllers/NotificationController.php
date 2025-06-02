<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FCMService;

class NotificationController extends Controller
{
    protected $fcm;

    public function __construct(FCMService $fcm)
    {
        $this->fcm = $fcm;
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $result = $this->fcm->sendFCMV1($request->fcm_token, $request->title, $request->body);

        if ($result) {
            return response()->json(['message' => 'Notifikasi berhasil dikirim']);
        } else {
            return response()->json(['error' => 'Gagal mengirim notifikasi'], 500);
        }
    }
}

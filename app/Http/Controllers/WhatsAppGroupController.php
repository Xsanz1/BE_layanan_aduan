<?php
namespace App\Http\Controllers;

use App\Services\WablasService;
use Illuminate\Http\Request;

class WhatsAppGroupController extends Controller
{
    protected $wablasService;

    public function __construct(WablasService $wablasService)
    {
        $this->wablasService = $wablasService;
    }

    /**
     * Send a message to a predefined WhatsApp group.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        // Only validate the message, since group_id is predefined
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = $request->input('message');
        $response = $this->wablasService->sendMessageToGroup($message);

        if ($response && isset($response['status']) && $response['status'] == 'success') {
            return response()->json(['success' => true, 'data' => $response], 200);
        }

        return response()->json(['success' => false, 'message' => 'Failed to send message', 'data' => $response], 500);
    }
}

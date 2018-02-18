<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    /**
     * Show the messages list.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $messages = Message::whereHas('address', function($query) use ($request) {
            return $query->whereHas('domain', function ($query) use ($request) {
                return $query->where('user_id', $request->user()->id);
            });
        })->orderBy('created_at', 'desc')->with('address')->paginate(20);

        return view('dashboard.messages.index', [
            'messages' => $messages,
        ]);
    }

    /**
     * View a single message.
     *
     * @param Message $message
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Message $message, Request $request)
    {
        $this->authorize('view', $message);

        return view('dashboard.messages.show', [
            'message' => $message,
        ]);
    }
}

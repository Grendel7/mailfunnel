<?php

namespace App\Http\Controllers;

use App\ReplyEmail;
use Illuminate\Http\Request;

class ReplyController extends Controller
{

    /**
     * Show the reply email form
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view('reply.create', ['request' => $request]);
    }

    /**
     * Validate and generate the reply email
     *
     * @param Request $request
     * @return string
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'from_name' => 'required',
            'from_email' => 'required|email',
            'to_name' => 'required',
            'to_email' => 'required|email',
        ]);

        $from = sprintf('%s <%s>', $request->get('from_name'), $request->get('from_email'));
        $to = sprintf('%s <%s>', $request->get('to_name'), $request->get('to_email'));

        return view('reply.show', ['email' => ReplyEmail::generate($from, $to)]);
    }
}
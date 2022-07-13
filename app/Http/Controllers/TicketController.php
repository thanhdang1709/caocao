<?php

namespace App\Http\Controllers;

use App\Jobs\SendFcm;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use App\Models\Notification;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function list()
    {
        $tickets = Ticket::with('user')->get();
        return view('ticket.list', compact('tickets'));
    }

    public function add(Request $request)
    {
        // $id = $request->id;
        // $ticket = Ticket::where('id', $id)->with('user')->get();
        // dd($ticket);
        return view('ticket.add');
    }

    public function sent(Request $request)
    {
        // dd('123');
        $user_id = $request->user_id;
        $user = User::find($user_id);
        $title = $request->title ?? "AZ World";
        $notification = $request->notification ?? "The first socialFi";

        $noti = new Notification();
        $noti->user_id = $request->user_id;
        $noti->subject = 'ticket';
        $noti->title = $request->title;
        $noti->content = $request->notification;
        $noti->read = 0;
        $noti->save();

        \Queue::push(new SendFcm($user = $user, $title, $notification));

        return $this->responseOK([]);
    }
}

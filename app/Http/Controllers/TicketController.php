<?php

namespace App\Http\Controllers;
use App\Jobs\SendFcm;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use App\Models\Notification;

class TicketController extends Controller
{
    public function list()
    {
        return 'hello';
    }

    public function add(Request $request)
    {
        $user = User::find(31);
        $title = $request->title ?? "AZ World";
        $notification = $request->notification ?? "New version updated!";

        $noti = new Notification();
        $noti->user_id = $request->user_id;
        $noti->subject = 'ticket';
        $noti->title = $request->title;
        $noti->content = $request->notification;
        $noti->read = 0;
        $noti->save();

        \Queue::push(new SendFcm($user, $title, $notification));

        
        return view('ticket.add');
    }

    public function sent(Request $request)
    {
        $user = User::find(31);
        $title = $request->title ?? "AZ World";
        $notification = $request->notification ?? "New version updated!";

        $noti = new Notification();
        $noti->user_id = $request->user_id;
        $noti->subject = 'ticket';
        $noti->title = $request->title;
        $noti->content = $request->notification;
        $noti->read = 0;
        $noti->save();


        \Queue::push(new SendFcm($user = $user, $title, $notification));

        return 'sent';
    }
}

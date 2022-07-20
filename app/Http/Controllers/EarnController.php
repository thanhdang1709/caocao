<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Earn;
use App\Models\User;
use Illuminate\Support\Carbon;
use App\Jobs\SendFcm;
use App\Models\Notification;

class EarnController extends Controller
{


    // private $user;


    public function __construct()
    {
        // $this->middleware(['check_token']);
        // $this->user = auth()->user();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {

        $subject = $request->subject ?? 1;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $status = $request->status ? explode(",", $request->status) : [];

        $subjects = [];

        if($subject == 1) {
            $subjects = ['tasks', 'spin', 'ads', 'donate', 'ref'];
        } else if ($subject == 2)  {
            $subjects = ['tasks'];
        } else if ($subject == 3) {
            $subjects = ['ads'];
        } else if ($subject == 4) {
            $subjects = ['spin'];
        } else if ($subject == 5) {
            $subjects = ['donate'];
        } else if ($subject == 6) {
            $subjects = ['ref'];
        } else if ($subject == 7) {
            $subjects = ['earn_dice', 'bet_dice'];
        } else if ($subject == 8) {
            $subjects = ['earn_offer'];
        }else {
            $subjects = [];
        }

        $filters = $request->all();

        // var_dump($filters);die;
        $queryEarn = Earn::query();

        //Add sorting

        $queryEarn->with('user');

        //Add Conditions

        // if(!is_null($filters['user_id'])) {
        //     $queryEarn->where('user_id','=',$filters['user_id']);
        // }
        $sort = $request->sort ?? 'id';

        if($sort && $sort == 'id') {
            $queryEarn->orderBy('id','desc');
        }
        if($sort && $sort == 'reward') {
            $queryEarn->orderBy('reward','desc');
        }

        if($status) {
            $queryEarn->whereIn('status', $status);
        }

        if($request->today) {
            $queryEarn->whereDate( 'created_at', '=', now()->subDays(1));
        }

        if($from_date && $to_date) {
            $queryEarn->whereDateBetween('created_at', $from_date, $to_date);
        }

        if($request->user_id) {
            $queryEarn->where('user_id','=', $request->user_id);
        }

        // if(!is_null($filters['state_id'])) {
        //     $queryEarn->whereHas('profile',function($q) use ($filters){
        //         return $q->where('state_id','=',$filters['state_id']);
        //     });
        // }

        // if(!is_null($filters['city_id'])) {
        //     $queryEarn->whereHas('profile',function($q) use ($filters){
        //         return $q->where('city_id','=',$filters['city_id']);
        //     });
        // }

        //Fetch list of results

        $earns = $queryEarn->WhereIn('subject', $subjects)->paginate(20);


        return view('earns.list', compact('earns'));
    }

    public function approve_task(Request $request)
    {
        $earn_id = $request->earn_id;
        $earn = Earn::where('id', $earn_id)->where('status', 1)->first();
        if($earn && $earn->reward > 0)
        {
            $user = User::where('id', $earn->user_id)->first();

            if($user->is_ban) {
                return;
            }

            $log_id = \DB::table('balance_logs')->insertGetId([
                'user_id' => $earn->user_id,
                'start_balance' => $user->balance,
                'start_pending_balance' => $user->pending_balance,
                'amount' => (double)$earn->reward,
                'description' => 'Partners approve reward task '.$earn->id,
            ]);
            User::where('id', $earn->user_id)->decrement('pending_balance', (double)$earn->reward);
            User::where('id', $earn->user_id)->increment('balance', (double)$earn->reward);

            $user = User::where('id', $earn->user_id)->first();
            $log  = \DB::table('balance_logs')->whereId($log_id)->update([
                'to_balance' => $user->balance,
                'to_pending_balance' => $user->pending_balance,
            ]);
            Earn::where('id', $earn_id)->where('status', 1)->update(['status' => 2]);

            $title = "AZ WORLD";
            $notification = "Partners approved reward task, please check history!";
            $noti = new Notification();
            $noti->user_id = $user->id;
            $noti->subject = 'reward';
            $noti->title = $title;
            $noti->content = $notification;
            $noti->read = 0;
            $noti->save();
            \Queue::push(new SendFcm($user,$title,$notification));
        }



        return $this->responseOK(['message' => 'OK']);
    }

    public function reject_task(Request $request)
    {
        $earn_id = $request->earn_id;
        $earn = Earn::where('id', $earn_id)->where('status', 1)->update(['status' => 3, 'reward' => 0, 'description' => 'Reject: You did not hold the token while the system was checking']);
        $earn = Earn::where('id', $earn_id)->first();

        if($earn)
        {

            $user = User::where('id', $earn->user_id)->first();
            $log_id = \DB::table('balance_logs')->insertGetId([
                'user_id' => $earn->user_id,
                'start_balance' => $user->balance,
                'start_pending_balance' => $user->pending_balance,
                'amount' => (double)$earn->reward,
                'description' => 'Partners reject reward task  '.$earn->id,
            ]);

            User::where('id', $earn->user_id)->decrement('pending_balance', (double)$earn->reward);

            $user = User::where('id', $earn->user_id)->first();
            $log  = \DB::table('balance_logs')->whereId($log_id)->update([
                'to_balance' => $user->balance,
                'to_pending_balance' => $user->pending_balance,
            ]);
            $title = "AZ WORLD";
            $notification = "Partners rejected task. Reason: You did not hold the token while the system was checking, please check history!";
            $noti = new Notification();
            $noti->user_id = $user->id;
            $noti->subject = 'reward';
            $noti->title = $title;
            $noti->content = $notification;
            $noti->read = 0;
            $noti->save();
            \Queue::push(new SendFcm($user,$title,$notification));

            return $this->responseOK('OK');
        }

        return $this->responseError('ERROR');


    }



    public function approve_user(Request $request)
    {
        $user_id = $request->user_id;
        $from_date = date(env('DATE_APPROVE_FROM'));
        $to_date = date(env('DATE_APPROVE_TO'));
        $earns = Earn::where('user_id', $user_id)->whereDateBetween('created_at', $from_date, $to_date)->where('status', 1)->get();
        $user = User::where('id', $user_id)->first();

        if($user->is_ban) {
            return;
        }

        $list_id = [];

        if($earns) {
            foreach($earns as $earn) {
                Earn::where('id', $earn->id)->update(['status' => 2]);
                array_push($list_id, $earn->id);
            }

        }

        if(count($list_id) > 0)
        {
            foreach($list_id as $earn_id) {
                $earn = Earn::where('id', $earn_id)->first();
                $log_id = \DB::table('balance_logs')->insertGetId([
                    'user_id' => $user_id,
                    'start_balance' => $user->balance,
                    'start_pending_balance' => $user->pending_balance,
                    'amount' => (double)$earn->reward,
                    'description' => 'Partners approve reward task  '.$earn->id,
                ]);
                User::where('id', $earn->user_id)->decrement('pending_balance', (double)$earn->reward);
                User::where('id', $earn->user_id)->increment('balance', (double)$earn->reward);

                $user = User::where('id', $earn->user_id)->first();
                $log  = \DB::table('balance_logs')->whereId($log_id)->update([
                    'to_balance' => $user->balance,
                    'to_pending_balance' => $user->pending_balance,
                ]);
            }

            $title = "AZ WORLD";
            $notification = "Partners approved reward task, please check history!";
            $noti = new Notification();
            $noti->user_id = $user->id;
            $noti->subject = 'reward';
            $noti->title = $title;
            $noti->content = $notification;
            $noti->read = 0;
            $noti->save();
            \Queue::push(new SendFcm($user,$title,$notification));
        }

        return $this->responseOK(['message' => 'OK']);
    }

    public function reject_user(Request $request)
    {
       $user_id = $request->user_id;
        $earns = Earn::where('user_id', $user_id)->whereDate('created_at',  date(env('DATE_APPROVE')))->where('status', 1)->get();
        $list_id = [];
        if($earns) {
            foreach($earns as $earn) {
                Earn::where('id', $earn->id)->update(['status' => 3, 'description' => 'Reject: You did not hold the token while the system was checking']);
                array_push($list_id, $earn->id);
            }

        }

        if(count($list_id) > 0)
        {
            foreach($list_id as $earn_id) {
                $earn = Earn::where('id', $earn_id)->first();
                $user = User::where('id', $user_id)->first();
                $log_id = \DB::table('balance_logs')->insertGetId([
                    'user_id' => $user_id,
                    'start_balance' => $user->balance,
                    'start_pending_balance' => $user->pending_balance,
                    'amount' => (double)$earn->reward,
                    'description' => 'Partners reject reward task  '.$earn->id,
                ]);
                User::where('id', $earn->user_id)->decrement('pending_balance', (double)$earn->reward);
                $user = User::where('id', $earn->user_id)->first();
                $log  = \DB::table('balance_logs')->whereId($log_id)->update([
                    'to_balance' => $user->balance,
                    'to_pending_balance' => $user->pending_balance,
                ]);
            }

            $title = "AZ WORLD";
            $notification = "Partners rejected task. Reason: You did not hold the token while the system was checking, please check history!";
            $noti = new Notification();
            $noti->user_id = $user->id;
            $noti->subject = 'reward';
            $noti->title = $title;
            $noti->content = $notification;
            $noti->read = 0;
            $noti->save();
            \Queue::push(new SendFcm($user,$title,$notification));
        }

        foreach($earns as $earn) {
            Earn::where('id', $earn->id)->update(['reward' => 0]);
        }

        return $this->responseOK(['message' => 'OK']);


    }

}

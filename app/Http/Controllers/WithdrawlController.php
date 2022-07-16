<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Earn;
use App\Models\User;
use App\Models\Withdraw;
use App\Jobs\SendFcm;

use Illuminate\Support\Carbon;

class WithdrawlController extends Controller
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

        $filters = $request->all();
        
        // var_dump($filters);die;
        $queryEarn = Withdraw::query();
        $queryEarn->with('user');
        $queryEarn->withCount([
            'user as sum_fcm_token' => function ($query) {
                    $query->select(\DB::raw("SUM(fcm_token) as sum_fcm_token"))->groupBy('fcm_token');
                }
            ]);

        //Add Conditions

        // if(!is_null($filters['user_id'])) {
        //     $queryEarn->where('user_id','=',$filters['user_id']);
        // }
        $order_by = $request->order_by ?? 'desc';
        $sort = $request->sort ?? 'id';

        if($sort && $sort == 'id') {
            $queryEarn->orderBy('id',$order_by);
        }
        if($sort && $sort == 'amount') {
            $queryEarn->orderBy('amount',$order_by);
        }
        if($sort && $sort == 'date') {
            $queryEarn->orderBy('created_at',$order_by);
        }

        if($status) {
            $queryEarn->whereIn('status', $status);
        }


        if($request->today) {
            $queryEarn->whereDate( 'created_at', '=', Carbon::now());
        }

        if($from_date && $to_date) {
            $queryEarn->whereDateBetween('created_at', $to_date, $from_date);
        }

        if($request->user_id) {
            $queryEarn->where('user_id','=', $request->user_id);
        }

        // if(!is_null($filters['state_id'])) {
        //     $queryEarn->whereHas('profile',function($q) use ($filters){
        //         return $q->where('state_id','=',$filters['state_id']);
        //     });
        // }

        //Fetch list of results

        $earns = $queryEarn->paginate(200);
        
        return view('withdraw.list', compact('earns'));
    }

    public function approve_request(Request $request)
    {
        $earn_id = $request->earn_id;
        $earn = Withdraw::where('id', $earn_id)->where('status', 1)->update(['status' => 2, 'description' => 'Successful, sent to your address']);
        $earn = Withdraw::where('id', $earn_id)->first();

        if($earn->amount > 0)
        {
            $user = User::where('id', $earn->user_id)->first();
            $log_id = \DB::table('balance_logs')->insertGetId([
                'user_id' => $earn->user_id,
                'start_balance' => $user->balance,
                'start_pending_balance' => $user->pending_balance,
                'amount' => (double)$earn->amount,
                'description' => 'Admin approve withdraw request '.$earn->id,
            ]);
            User::where('id', $earn->user_id)->decrement('frozen', (double)$earn->amount);

            $user = User::where('id', $earn->user_id)->first();
            $log  = \DB::table('balance_logs')->whereId($log_id)->update([
                'to_balance' => $user->balance,
                'to_pending_balance' => $user->pending_balance,
            ]);
        }
            
        return $this->responseOK(['message' => 'OK']);
    }

    public function reject_request(Request $request)
    {
        $earn_id = $request->earn_id;
        $earn = Withdraw::where('id', $earn_id)->where('status', 1)->update(['status' => 3, 'description' => 'Reject: You did not hold the AZW token while the system was checking']);
        $earn = Withdraw::where('id', $earn_id)->first();

        if($earn)
        {

            $user = User::where('id', $earn->user_id)->first();
            $log_id = \DB::table('balance_logs')->insertGetId([
                'user_id' => $earn->user_id,
                'start_balance' => $user->balance,
                'start_pending_balance' => $user->pending_balance,
                'amount' => (double)$earn->amount,
                'description' => 'Admin reject withdraw request '.$earn->id,
            ]);

            User::where('id', $earn->user_id)->decrement('frozen', (double)$earn->amount);
            
            $user = User::where('id', $earn->user_id)->first();
            $log  = \DB::table('balance_logs')->whereId($log_id)->update([
                'to_balance' => $user->balance,
                'to_pending_balance' => $user->pending_balance,
            ]);
            return $this->responseOK('OK');
        }

        return $this->responseError('ERROR');
            
       
    }
    
}

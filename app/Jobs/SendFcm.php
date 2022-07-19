<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendFcm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $data;
    protected $notification;
    protected $title;
    //public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     * @return void
     */
    public function __construct($user, $notification = null, $title = "", $data = null)
    {
       $this->user = $user;
       $this->data = $data;
       $this->notification = $notification;
       $this->title = $title;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $ids = [];
        array_push($ids, $this->user->fcm_token);
        array_push($ids, env('FCM_ADMIN_CLIENT_TOKEN'));
        $response = Http::withHeaders([
            'Authorization' => env('FCM_ADMIN_TOKEN'),
            'Content-Type' => 'application/json' 
        ])->post('https://fcm.googleapis.com/fcm/send', [
            'registration_ids' => $ids,
            'notification'=> [
                "title"=> $this->notification,
                "body"=> $this->title,
                "sound" => "default"
            ],
            "data"=>[
                "click_action"=> "FLUTTER_NOTIFICATION_CLICK",
                "priority"=> "high",
                "collapse_key"=> "type_a",
                "data"=> $data ?? [
                    "click_action"=> "FLUTTER_NOTIFICATION_CLICK",
                    "other_data"=> [ 'type' => 'WITHDRAW' ],
                    "title"=> "title",
                    "body"=> "body",
                    "priority"=> "high",
                    "click_action"=> "FLUTTER_NOTIFICATION_CLICK"
                ]
                ],
        ]);
        return $response;
    }
}

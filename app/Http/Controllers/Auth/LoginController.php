<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'pass' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return $this->responseError($validator->errors(), 422);
        }

        $email = $request->email;
        if (!$request->changepass) {
            $user = User::where('email', $email)->first();
            if (!$user) {
            } else {
                return $this->responseError('User already exists!', 201);
            }
        }


        if (!in_array($email, $this->email_allow)) {
            \Queue::push(new SentMailVerify($email));
            // VerificationCode::send($email);
            return $this->responseOK(null, 'Sent verification code');
        } else {
            return $this->responseError('Please contact admin for Beta Test!', 201);
        }
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'pass' => 'required|min:6',
            'verify_code' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $code = $request->verify_code;
        $email = $request->email;
        $ref_code = strtoupper($request->ref_code);

        if (!in_array($email, $this->email_allow)) {

            if (VerificationCode::verify($code, $email)) {

                $user = User::where('email', $email)->first();
                if (!$user) {
                    User::create(array_merge(
                        $validator->validated(),
                        ['password' => bcrypt($request->pass), 'code' => $this->genCode(6), 'name' => env('APP_NAME') . '_' . rand(10000, 99999)]
                    ));
                    $user = User::where('email', $email)->first();
                    $user->following()->attach(1);
                } else {
                    return $this->responseError('User already exists!', 201);
                }


                $user = User::where('email', $email)->first();
                if ($user) {
                    if ($user->ref_code) {
                    } else {
                        // if( $ref_code ) {
                        if ($user->code != $ref_code) {
                            $check_code = User::where('code', $ref_code)->first();
                            if ($check_code) {
                                User::where('id', $user->id)->update(['ref_code' => $ref_code]);

                                $price = $this->getPrice();
                                $reward =  (float)env('POINT_REWARD_REF') / $price;

                                $total_earn = Earn::where('user_id', $user->id)->where('subject', 'ref')->whereDate('created_at', Carbon::today())->count();
                                if ($total_earn < (int)env('LIMIT_ADS_VIDEO')) {
                                    \DB::table('earns')->insert(['user_id' => $check_code->id, 'status' => 1, 'reward' => $reward, 'subject' => 'ref', 'description' => 'Reward from referral', 'created_at' => Carbon::now()]);
                                    User::where('id', $check_code->id)->increment('pending_balance',  $reward);
                                }
                            }
                            //  else {
                            //     return $this->responseError('Invalid referral code', 201);
                            // }
                        }

                        // } else {
                        //     return $this->responseError('Referral code required', 201);
                        // }

                    }
                }

                return $this->responseOK("Register new account success!", 200);
            } else {

                return $this->responseError('Verification code is incorrect', 201);
            }
        } else {
            return $this->responseError('Please contact admin for Beta Test!', 201);
        }
    }


    public function changepass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'pass' => 'required|min:6',
            'code' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $code = $request->verify_code;
        $email = $request->email;
        $ref_code = strtoupper($request->ref_code);

        if (!in_array($email, $this->email_allow)) {

            if (VerificationCode::verify($code, $email)) {

                $user = User::where('email', $email)->first();
                if ($user->is_ban) {
                    return $this->responseError('Your account banned!', 201);
                }
                if ($user) {
                    User::where('email', $email)->update(
                        ['password' => bcrypt($request->pass)]
                    );
                } else {
                    return $this->responseError('The account does not exist!', 201);
                }

                return $this->responseOK("Change new password success!", 200);
            } else {

                return $this->responseError('Verification code is incorrect', 201);
            }
        } else {
            return $this->responseError('Please contact admin for Beta Test!', 201);
        }
    }


    public function login(Request $request)
    {

        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:6',
            // 'g-recaptcha-response' => 'required|recaptcha',
        ];
        // $messages = [
        //     'g-recaptcha-response.recaptcha'   => 'loi captcha',
        // ];
        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return $this->respondWithErrorMessage($validator);
        }
        $email = $request->email;

        $credentials = $request->only(['email']);

        if ((auth()->attempt(['email' => $credentials['email'], 'password' => ($request->password)]))) {
            if (auth()->user()->is_admin == 1) {
                return redirect()->route('home');
            } else {
                return redirect()->back()
                ->with('error', 'You do not have permission to login.');
            }
        } else {
            return redirect()->back()
                ->with('error', 'Email-Address And Password Are Wrong.');
        }

    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return redirect()->route('login');
    }

    public function guard()
    {
        return Auth::guard('api');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth()->user());
    }


    public function changePassWord(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $userId = auth()->user()->id;

        $user = User::where('id', $userId)->update(
            ['password' => bcrypt($request->new_password)]
        );

        return response()->json([
            'message' => 'User successfully changed password',
            'user' => $user,
        ], 201);
    }
}

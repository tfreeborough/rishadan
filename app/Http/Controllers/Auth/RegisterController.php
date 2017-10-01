<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\UserEmailVerification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Mail\VerificationEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'verified' => $data['verified']
        ]);
    }

        /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $data = $request->all();
        $data['verified'] = false;

        event(new Registered($user = $this->create($data)));

        return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());
    }

        /**
     * The user has been registered. Send verification email and store a record in the verifications table
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        $verification = UserEmailVerification::create([
            'user_id' => $user->id,
            'token' => uuid::generate(),
            'expires' => Carbon::now()->addDay(),
        ]);
        $this->sendVerificationEmail($user->email, $verification->token);
    }
        /**
     * Send a verification email to the email provided
     *
     * @param  string $email
     * @param  string $token
     * @return void
     */
    protected function sendVerificationEmail($email, $token) {
        Mail::to($email)->send(new VerificationEmail($token));
    }

        /**
     * Verify account from token link
     *
     * @param  string $token
     * @return redirect
     */
    public function verify($token) {
        $verification = UserEmailVerification::where('token', $token)->first();
        if ($verification) {
            if (Carbon::now()->lt(Carbon::parse($verification->expires))) {
                $user = $verification->user;
                $user->verified = true;
                $user->save();
                session(['message' => 'Verification Successful!']);
                Auth::guard()->login($user);
                return redirect('/');
            } else {
                $verification->delete();
                session(['message' => 'Verification Expired!']);
                return redirect('/');
            }
        } else {
            abort(502, 'Token Not Found');
        }
    }
}

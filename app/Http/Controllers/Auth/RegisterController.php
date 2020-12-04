<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\PhoneHelper;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Traits\AuthenticatedRedirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{

    use AuthenticatedRedirect;

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
     * Length of the verification code that will be generated for
     * checking phone number
     *
     * @var integer
     */
    public static $VerifyCodeLength = 6;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
	public function redirectTo()
	{
		return app()->getLocale() . '/';
	}

    /**
     * Time in seconds when verification code will be expired
     *
     * @var integer
     */
    protected $timeToVerifySMSExpire = 300;

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
     * {@inheritdoc}
     *
     * @static
     */
    public function showRegistrationForm()
    {
        return view('auth.register', [
            'phones' => PhoneHelper::phoneAssoc(),
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(RegistrationRequest $request)
    {
        $inputs = $request->validated();

        event(new Registered($user = $this->create($inputs)));

        $this->guard()->login($user);

        return redirect($this->generateAuthPath($user));
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'phone' => $data['phoneCode'] . $data['phone'],
            'password' => Hash::make($data['password']),
            'phone_verification_code' => $this->sendVerificationSMS(),
        ]);
    }

    protected function sendVerificationSMS()
    {
        $verificationCode = 'qwerty'; //Str::random(self::$VerifyCodeLength);
        $verificationCodeExpiration = time() + $this->timeToVerifySMSExpire;

        // TODO: send verification SMS

        return Hash::make($verificationCode) . '_' . $verificationCodeExpiration;
    }

}

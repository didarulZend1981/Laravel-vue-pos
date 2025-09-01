<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use App\Models\User;
use Inertia\Inertia;
use App\Mail\OTPMail;
use App\Helper\JWTToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    //===========================registraion management=====================//
    //registraion page
    public function showSignUp(){
        return Inertia::render('Auth/RegistrationPage');
    }

    public function showUser(Request $request){
        $users = User::orderBy('id', 'desc')->get();
        return Inertia::render('Dashboard/User/UserPage', ['users' => $users]);
    }

    //==========================show User create page=================//
    public function showSaveUser(Request $request){
        $id = $request->query('id');
        $user = User::where('id', $id)->first();

        return Inertia::render('Dashboard/User/UserSavePage', [
            'user' => $user, // Pass brand as key-value pair
        ]);
    }

    //registraion
    public function signUp(Request $request){
        // dd($request->all());
        // $check=!$request->file('image')?'null':'a';
        // dd($check);
        $request->validate([
            'first_name' => 'required|min:4|max:50',
            'last_name' => 'required|alpha|min:4|max:50',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|numeric|digits_between:11,15|unique:users,mobile',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string|min:4|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {

            $image_url = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageNameToStore = 'user-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image_url = $image->storeAs('user', $imageNameToStore, 'public');
            }

            $check=!$request->file('image')?NULL:$imageNameToStore;
            // Create the user
            User::create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'role' => $request->input('role'),
                'mobile' => $request->input('mobile'),
                'address' => $request->input('address'),
                'password' => bcrypt($request['password']),
                'image' => $check,
            ]);

            $data = ['message' => 'Registration successful !!', 'status' => true, 'code' => 201];
            return redirect()->route('user.page')->with($data);
        } catch (Exception $e) {
            $data = ['message' => 'Registration failed !!', 'status' => false, 'code' => 400];
            return redirect()->route('user.page')->with($data);
        }
    }

    //====================role update====================

    public function updateRole(Request $request, $id){
        $user = User::findOrFail($id);

        // Validate the role input
        $request->validate([
            'role' => 'required|in:1,2,3', // Only allow specific roles
        ]);


        $user->role = $request->role;
        $user->save();

        $data = ['message' => 'Role updated successfully !!', 'status' => true, 'code' => 201];

        return redirect()->route('user.page')->with($data);
    }

    //=========================delete user=======================//
    public function deleteUser(Request $request, $id){

        // dd($user_id);
        // $user = User::where('id', $id)->first();
         $user = User::findOrFail($id);



         if (!empty($user->image)) {
                    // event_image এর path থেকে basename (ফাইল নাম) বের করা
                    $fileName = basename($user->image); // উদাহরণ: event_image_17234543.jpg

                    // Step 3: স্টোরেজ path ঠিক করা
                    $filePath = 'user/' . $fileName;

                    // Step 4: ফাইলটি থাকলে মুছে ফেলা
                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }
                }


        $buser =   $user->delete();
        if (!$buser) {
            $data = ['message' => 'User not found', 'status' => false, 'code' => 404];
             return redirect()->route('user.page')->with($data);
        }
        $data = ['message' => 'User deleted successfully', 'status' => true, 'code' => 200];
         return redirect()->route('user.page')->with($data);
    }

    //================================login management========================//
    //login page
    public function showSignIn()
    {
        return Inertia::render('Auth/LoginPage');
    }

    //sign in
    public function signIn(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            // Password match
            $token = JWTToken::createToken($request->input('email'), $user->id);

            $data = ['message' => 'Login successful', 'status' => true, 'code' => 200];

            return redirect()
                ->route('show.dashboard')
                ->with($data)
                ->cookie('token', $token, 60 * 24 * 7);
        } else {
            // Password doesn't match or user not found
            $data = ['message' => 'Invalid email or password', 'status' => false, 'code' => 401];
            return redirect()->back()->with($data);
        }
    }

    //================================otp management========================//
    //otp page
    public function showOTPPage()
    {
        return Inertia::render('Auth/SendOtpPage');
    }

    //otp send
    public function sendOTP(Request $request)
    {
        $email = request()->input('email');
        $otp = rand(10000, 99999);
        $user = User::where('email', $email)->first();

        if ($user !== null) {
            Mail::to($email)->send(new OTPMail($otp));

            User::where('email', $email)->update(['otp' => $otp, 'updated_at' => now()]);
            $data = ['message' => 'OTP sent successfully', 'status' => true, 'code' => 200];
            $request->session()->put('email', $email);
            return redirect()->route('verify.OTP.page')->with($data);
        } else {
            $data = ['message' => 'Email not found', 'status' => false, 'code' => 404];
            return redirect()->back()->with($data);
        }
    }

    //verify otp page
    public function showVerifyOTPPage()
    {
        return Inertia::render('Auth/VerifyOTPPage');
    }

    //otp verify
    public function verifyOTP(Request $request)
    {
        $otp = $request->input('otp');
        $email = $request->session()->get('email', 'default');
        $user = User::where('email', $email)->where('otp', $otp)->first();

        if ($user !== null) {
            $otpGeneratedTime = $user->updated_at;
            if ($otpGeneratedTime && $otpGeneratedTime->diffInMinutes(now()) <= 1) {
                //update otp
                $user->update(['otp' => 0]);

                //generete token
                $token = JWTToken::createTokenForResetPassword($email);
                $data = ['message' => 'OTP verified successfully', 'status' => true, ' code' => 200];
                return redirect()->route('new.password.page')->with($data)->cookie('token', $token, 5);
            } else {
                $data = ['message' => 'OTP Expired', 'status' => false, ' code' => 400];
                $request->session()->flush();
                return redirect()->back()->with($data);
            }
        } else {
            $data = ['message' => 'Invalid OTP', 'status' => false, ' code ' => 404];
            return redirect()->back()->with($data);
        }
    }

    //================================new passwrod management========================//
    //new password page
    public function showNewPasswordPage()
    {
        return Inertia::render('Auth/ResetPasswordPage');
    }

    //ser new password
    public function newPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|string|confirmed',
        ]);

        $email = $request->header('email');
        $password = $request->input('password');
        $password_update = User::where('email', $email)->update(['password' => bcrypt($password)]);

        if ($password_update) {
            $data = ['message' => 'Password updated successfully', 'status' => true, ' code' => 200];
            Cookie::queue(Cookie::forget('token'));
            return to_route('signIn.page')->with($data);
        } else {
            $data = ['message' => 'Password not updated', 'status' => false, ' code' => 400];
            return redirect()->back()->with($data);
        }
    }

    //================================logout =====================================//
    public function logout()
    {
        return redirect()
            ->route('signIn.page')
            ->cookie('token', '', -1);
    }
}

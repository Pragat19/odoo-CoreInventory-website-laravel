<?php

namespace App\Http\Controllers\Api;

use App\Constants\Keys;
use App\Constants\ResponseCodes;
use App\Http\Controllers\BaseController;
use App\Mail\ForgotPasswordOtp;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:15',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $this->addSuccessResultKeyValue(Keys::TOKEN, $user->createToken('CoreInventory')->accessToken);
        $this->addSuccessResultKeyValue(Keys::USER, $user);
        $this->setSuccessMessage('Registration successful.');
        return $this->sendSuccessResult();
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:6|different:current_password',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Current password is incorrect.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        $this->setSuccessMessage('Password changed successfully.');
        return $this->sendSuccessResult();
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Invalid email or password.');
            return $this->sendFailResultWithCode(ResponseCodes::UNAUTHORIZED_USER);
        }

        $user = Auth::user();
        $this->addSuccessResultKeyValue(Keys::TOKEN, $user->createToken('CoreInventory')->accessToken);
        $this->addSuccessResultKeyValue(Keys::USER, $user);
        $this->setSuccessMessage('Login successful.');
        return $this->sendSuccessResult();
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_otps')->updateOrInsert(
            ['email' => $request->email],
            ['otp' => $otp, 'expires_at' => Carbon::now()->addMinutes(10), 'updated_at' => Carbon::now(), 'created_at' => Carbon::now()]
        );

        Mail::to($request->email)->send(new ForgotPasswordOtp($otp));

        $this->setSuccessMessage('OTP sent to your email.');
        return $this->sendSuccessResult();
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $record = DB::table('password_otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$record) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Invalid OTP.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        if (Carbon::now()->gt(Carbon::parse($record->expires_at))) {
            $this->addFailResultKeyValue(Keys::ERROR, 'OTP has expired.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $this->setSuccessMessage('OTP verified successfully.');
        return $this->sendSuccessResult();
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'        => 'required|email|exists:users,email',
            'otp'          => 'required|string|size:6',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $record = DB::table('password_otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$record) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Invalid OTP.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        if (Carbon::now()->gt(Carbon::parse($record->expires_at))) {
            $this->addFailResultKeyValue(Keys::ERROR, 'OTP has expired.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->new_password),
        ]);

        DB::table('password_otps')->where('email', $request->email)->delete();

        $this->setSuccessMessage('Password reset successfully.');
        return $this->sendSuccessResult();
    }

    public function profile(Request $request)
    {
        $this->addSuccessResultKeyValue(Keys::USER, Auth::user());
        $this->setSuccessMessage('Profile fetched successfully.');
        return $this->sendSuccessResult();
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name'  => 'sometimes|string|max:100',
            'phone' => 'sometimes|nullable|string|max:15',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $user->update($request->only('name', 'email', 'phone'));

        $this->addSuccessResultKeyValue(Keys::USER, $user->fresh());
        $this->setSuccessMessage('Profile updated successfully.');
        return $this->sendSuccessResult();
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        $this->setSuccessMessage('Logged out successfully.');
        return $this->sendSuccessResult();
    }

    function unauthorised()
    {
        $this->addFailResultKeyValue(Keys::ERROR, 'Unauthorised User');
        return $this->sendFailResultWithCode(ResponseCodes::UNAUTHORIZED_USER);
    }

    /**
     * Called When admin Services Access by none Admin User.
     */
    function adminaccess()
    {
        $this->addFailResultKeyValue(Keys::ERROR, 'Service Allow only for Admin . ');
        return $this->sendFailResultWithCode(ResponseCodes::UNAUTHORIZED_USER);
    }

    /**
     * Called When Active User's Services Access by none Un - Active User .
     */
    function activeaccess()
    {
        $this->addFailResultKeyValue(Keys::ERROR, 'You don\'t have access to use this service.');
        $this->addFailResultKeyValue(Keys::DATA, Auth::user());
        return $this->sendFailResultWithCode(ResponseCodes::INACTIVE_USER);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
<<<<<<< HEAD
        // ✅ Trim email (fix: trailing/leading spaces from Postman or frontend)
        $request->merge([
            'email' => trim((string) $request->email),
        ]);

        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username|alpha_dash',
            // ✅ FIX: remove dns check for local testing
            'email'    => 'required|email|unique:users,email',
=======
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users|alpha_dash',
            'email'    => 'required|email:rfc,dns|unique:users',
>>>>>>> 7613274ff59c99e80a4ab83874040fea620c9c08
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

<<<<<<< HEAD
        // ✅ Token valid 30 days
        $token = $user->createToken('auth-token')->plainTextToken;
=======
        $token = $user->createToken('auth-token', ['*'], now()->addDays(30))->plainTextToken;
>>>>>>> 7613274ff59c99e80a4ab83874040fea620c9c08

        return response()->json([
            'status'  => true,
            'message' => 'User registered successfully',
            'token'   => $token,
            'user'    => $this->formatUser($user),
        ], 201);
    }

    /**
     * Login and return token.
     */
    public function login(Request $request)
    {
<<<<<<< HEAD
        // ✅ Trim email too
        $request->merge([
            'email' => trim((string) $request->email),
        ]);

=======
>>>>>>> 7613274ff59c99e80a4ab83874040fea620c9c08
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

<<<<<<< HEAD
        // ✅ Optional: delete old tokens (uncomment if you want 1 device only)
        // $user->tokens()->delete();

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => $this->formatUser($user),
=======
        // Revoke old tokens to avoid accumulation (optional: keep if multi-device needed)
        // $user->tokens()->delete();

        $token = $user->createToken('auth-token', ['*'], now()->addDays(30))->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token'  => $token,
            'user'   => $this->formatUser($user),
>>>>>>> 7613274ff59c99e80a4ab83874040fea620c9c08
        ]);
    }

    /**
     * Get the authenticated user's profile.
     */
    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'data'   => $this->formatUser($request->user()),
        ]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

<<<<<<< HEAD
        // ✅ Trim email if exists
        if ($request->has('email')) {
            $request->merge([
                'email' => trim((string) $request->email),
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|required|string|max:255',
            'username' => 'sometimes|required|string|max:255|alpha_dash|unique:users,username,' . $user->id,
            'email'    => 'sometimes|required|email|unique:users,email,' . $user->id,
=======
        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|required|string|max:255',
            'username' => 'sometimes|required|string|max:255|alpha_dash|unique:users,username,' . $user->id,
            'email'    => 'sometimes|required|email:rfc,dns|unique:users,email,' . $user->id,
>>>>>>> 7613274ff59c99e80a4ab83874040fea620c9c08
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($validator->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Profile updated successfully',
            'data'    => $this->formatUser($user->fresh()),
        ]);
    }

    /**
     * Change the authenticated user's password.
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password'         => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Current password is incorrect'
            ], 403);
        }

        $user->update(['password' => Hash::make($request->password)]);

<<<<<<< HEAD
        // ✅ Logout from all devices after password change
=======
        // Revoke all tokens so user must re-login everywhere
>>>>>>> 7613274ff59c99e80a4ab83874040fea620c9c08
        $user->tokens()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Password changed successfully. Please log in again.',
        ]);
    }

    /**
<<<<<<< HEAD
     * Logout from current device.
=======
     * Logout from current device (revoke current token).
>>>>>>> 7613274ff59c99e80a4ab83874040fea620c9c08
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
<<<<<<< HEAD
     * Logout from all devices.
=======
     * Logout from all devices (revoke all tokens).
>>>>>>> 7613274ff59c99e80a4ab83874040fea620c9c08
     */
    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logged out from all devices successfully',
        ]);
    }

    /**
<<<<<<< HEAD
     * Return safe user fields.
=======
     * Return safe user fields (exclude password and sensitive data).
>>>>>>> 7613274ff59c99e80a4ab83874040fea620c9c08
     */
    private function formatUser(User $user): array
    {
        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'username'   => $user->username,
            'email'      => $user->email,
            'created_at' => $user->created_at?->toISOString(),
        ];
    }
}
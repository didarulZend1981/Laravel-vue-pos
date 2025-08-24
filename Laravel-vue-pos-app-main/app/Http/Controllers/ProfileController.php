<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    //================================update profile=============================//
    //get user profile
    public function getProfile(Request $request)
    {
        $email = $request->header('email');
        $user = User::where('email', $email)->first();
        return response()->json(
            [
                'status' => 'success',
                'message' => 'Profile retrieved successfully.',
                'user' => $user,
            ],
            200,
        );
    }

    //update user profile
    public function updateProfile(Request $request)
    {
        $email = $request->header('email');
        $user = User::where('email', $email)->first();

        $request->validate([
            'first_name' => 'required|min:4|max:50',
            'last_name' => 'required|min:4|max:50',
            'mobile' => 'required|numeric|digits_between:11,15|unique:users,mobile,' . $user->id,
            'address' => 'nullable|string|min:4|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // Handle Image Upload
            if ($request->hasFile('image')) {
                if ($user->image) {
                    Storage::disk('public')->delete($user->image);
                }

                $image = $request->file('image');
                $imageNameToStore = 'user-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image_url = $image->storeAs('user', $imageNameToStore, 'public');
                $user->image = $image_url;
            }

            // Update user data
            $user->update([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'mobile' => $request->input('mobile'),
                'address' => $request->input('address'),
            ]);

            $data = ['message' => 'Profile updated successfully.', 'status' => true, 'code' => 200];
            return redirect()->back()->with($data);
        } catch (Exception $e) {
            $data = ['message' => 'Error updating profile.', 'status' => false, 'code' => 400];
            return redirect()->back()->with($data);
        }
    }
}

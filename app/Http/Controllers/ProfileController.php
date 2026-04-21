<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\NotificationPreference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user()->load('notificationPreference'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        NotificationPreference::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'todo_notifications' => $request->boolean('todo_notifications', true),
                'document_notifications' => $request->boolean('document_notifications', true),
                'financial_notifications' => $request->boolean('financial_notifications', true),
                'reminder_notifications' => $request->boolean('reminder_notifications', true),
                'approval_notifications' => $request->boolean('approval_notifications', true),
            ]
        );

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Upload profile photo.
     */
    public function uploadPhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();

        // Delete old photo if exists
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Upload new photo
        $path = $request->file('profile_photo')->store('profile-photos', 'public');
        
        $user->update([
            'profile_photo_path' => $path,
        ]);

        return Redirect::route('profile.edit')->with('status', 'photo-updated');
    }

    /**
     * Remove profile photo.
     */
    public function removePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->update([
                'profile_photo_path' => null,
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'photo-removed');
    }
}

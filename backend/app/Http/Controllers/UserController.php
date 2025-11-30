<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // List users
    public function index()
    {
        return User::select("id", "name", "email", "created_at")
                  ->orderBy("id", "DESC")
                  ->paginate(10);
    }

    // Create
    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:6"
        ]);

        $validated["password"] = Hash::make($validated["password"]);

        return User::create($validated);
    }

    // Show
    public function show($id)
    {
        return User::findOrFail($id);
    }

    // Update
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            "name" => "required|string|max:255",
            "email" => [
                "required",
                "email",
                Rule::unique("users")->ignore($user->id)
            ],
        ]);

        $user->update($validated);

        return response()->json(["message" => "User updated"]);
    }

    // Delete
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(["message" => "User deleted"]);
    }

    // User Changes Own Password
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            "old_password" => "required",
            "new_password" => "required|min:6",
        ]);

        $user = auth()->user();

        if (!Hash::check($validated["old_password"], $user->password)) {
            return response()->json(["message" => "Old password incorrect"], 422);
        }

        $user->password = Hash::make($validated["new_password"]);
        $user->save();

        return response()->json(["message" => "Password changed"]);
    }

    // Admin Resets Password
    public function resetPassword(Request $request, $id)
    {
        $validated = $request->validate([
            "new_password" => "required|min:6"
        ]);

        $user = User::findOrFail($id);
        $user->password = Hash::make($validated["new_password"]);
        $user->save();

        return response()->json(["message" => "Password reset"]);
    }
}

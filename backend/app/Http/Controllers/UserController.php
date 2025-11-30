<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // List users
    // public function index()
    // {
    //     return User::select("id", "name", "email", "created_at")->with(['creator', 'updater'])
    //               ->orderBy("id", "DESC")
    //               ->paginate(10);
    // }
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $perPage    = $request->input('per_page', 10);
        $sortBy     = $request->input('sort_by', 'id');
        $sortDir    = $request->input('sort_dir', 'desc');

        $query = User::select('id', 'name', 'email', 'created_at', 'updated_at', 'created_by', 'updated_by')
                    ->with(['creator:id,name', 'updater:id,name']);

        // --- Search ---
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
            });
        }

        // --- Sorting ---
        if (in_array($sortBy, ['id', 'name', 'email', 'created_at'])) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('id', 'desc');
        }

        return $query->paginate($perPage);
    }


    // Create
    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:6"
        ]);
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id(); // first update = creator

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

        $validated['updated_by'] = auth()->id();

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
        $user->updated_by = auth()->id();
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
        $user->updated_by = auth()->id();
        $user->save();

        return response()->json(["message" => "Password reset"]);
    }
}

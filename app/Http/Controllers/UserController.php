<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Validator;

class UserController extends Controller
{
    public function getlist(Request $request)
    {
        $user = new User;
        if ($request->exists("role")) {
            $user = $user->where("role", $request->get("role"));
        }
        $user = $user->paginate();
        return response()->json(['user' => $user, 'message' => 'User received successfully'], 201);
    }
    // Register method
    public function register(Request $request)
    {

        if (!empty($request->input('id'))) {
            $user = User::findOrFail($request->input('id'));
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role = $request->role;
            $user->save();
            return response()->json(['user' => $user, 'message' => 'User updated successfully'], 201);

        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'role' => 'required|string'
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            return response()->json(['user' => $user, 'message' => 'User created successfully'], 201);

        }
    }

    // Login method
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid login details'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token], 200);
    }
    // Profile method
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }
    public function profileById(Request $request, $id)
    {
        return response()->json(User::findOrFail($id));
    }
    public function deleteUser(Request $request, $id)
    {
        User::destroy($id);
        return response()->json(["message" => "success"]);
    }
    public function logout(Request $request)
    {
        return response()->json(["message" => "success"]);
    }

    // Update profile method
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8',
        ]);

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json(['user' => $user, 'message' => 'Profile updated successfully'], 200);
    }
}

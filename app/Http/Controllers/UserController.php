<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
    ]);

    DB::table('users')->insert([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json(['message' => 'User created successfully'], 201);
}
public function index()
{
    $users = DB::table('users')->get();
    return response()->json($users);
}
public function show($id)
{
    $user = DB::table('users')->where('id', $id)->first();

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    return response()->json($user);
}
public function update(Request $request, $id)
{
    $user = DB::table('users')->where('id', $id)->first();

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $data = [];

    if ($request->has('name')) {
        $data['name'] = $request->name;
    }

    if ($request->has('email')) {
        $data['email'] = $request->email;
    }

    if ($request->has('password')) {
        $data['password'] = Hash::make($request->password);
    }

    $data['updated_at'] = now();

    DB::table('users')->where('id', $id)->update($data);

    return response()->json(['message' => 'User updated']);
}
public function destroy($id)
{
    $deleted = DB::table('users')->where('id', $id)->delete();

    if (!$deleted) {
        return response()->json(['message' => 'User not found'], 404);
    }

    return response()->json(['message' => 'User deleted']);
}

}

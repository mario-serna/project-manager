<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

// UsersController
class UController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    function getAll(Request $request)
    {
        if ($request->isJson()) {
            $users = User::all();
            return response()->json($users, 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function getUser(Request $request, $id)
    {
        if ($request->isJson()) {
            try {
                $user = User::findOrFail($id);

                return response()->json($user, 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'No content'], 406);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function getUsersByTerm(Request $request, $term, $limit = 5)
    {
        if ($request->isJson()) {
            if ($term !== '') {
                $users = User::select('id', 'username', 'email')
                ->where('username', 'like', "$term%")
                ->orWhere('email', 'like', "$term%")
                ->limit($limit)->get();
                return response()->json($users, 200);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function createUser(Request $request)
    {
        if ($request->isJson()) {
            $data = $request->json()->all();

            $usernameExist = User::where('username', $data['username'])->count();
            $emailExist = User::where('email', $data['email'])->count();

            if ($usernameExist > 0) {
                return response()->json(['error' => 'Username already exists!'], 406, []);
            } elseif ($emailExist > 0) {
                return response()->json(['error' => 'Email already exists!'], 406, []);
            }

            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'api_token' => str_random(60)
            ]);
            return response()->json($user, 201);
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function updateUser(Request $request, $id)
    {
        if ($request->isJson()) {
            try {
                $user = User::findOrFail($id);

                $data = $request->json()->all();
                
                $user->name = $data['name'];
                $user->username = $data['username'];
                $user->email = $data['email'];
                $user->level = $data['level'];
                $user->access = $data['access'];
                // $user->api_token = str_random(60);
                // $user->password = Hash::make($data['password']);

                $user->save();

                return response()->json($user, 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'No content'], 406);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function deleteUser(Request $request, $id)
    {
        if ($request->isJson()) {
            try {
                $user = User::findOrFail($id);

                $user->delete();

                return response()->json($user, 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'No content'], 406);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function getToken(Request $request)
    {
        if ($request->isJson()) {
            try {
                $data = $request->json()->all();

                $user = User::where('username', $data['username'])->first();

                if ($user && Hash::check($data['password'], $user->password)) {
                    return response()->json($user, 200);
                } else {
                    return response()->json(['error' => 'No content'], 406);
                }
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'No content'], 406);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    //
}

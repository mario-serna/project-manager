<?php

namespace App\Http\Controllers;

use App\User;
use App\Tutor;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TutorsController extends Controller
{
    function getAll(Request $request)
    {
        if ($request->isJson()) {
            $tutors = Tutor::join('users','users.id','=','tutors.user_id')
            ->select('tutors.*', 'users.fullname', 'users.username', 'users.email')
            ->get();
            return response()->json($tutors, 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function getTutor(Request $request, $id)
    {
        if ($request->isJson()) {
            try {
                $tutor = Tutor::findOrFail($id);
                $user = User::findOrFail($tutor->user_id);

                return response()->json(['tutor'=>$tutor, 'user'=>$user], 201);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'No content'], 406);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function getTutorsByTerm(Request $request, $term, $limit = 5)
    {
        if ($request->isJson()) {
            if ($term !== '') {
                $tutors = Tutor::select('id','name','lastname','m_lastname')
                ->where('lastname', 'like', "$term%")
                ->orWhere('name', 'like', "$term%")
                ->limit($limit)->get();
                return response()->json($tutors, 200);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }
    
    function createTutor(Request $request)
    {
        if ($request->isJson()) {
            $data = $request->json()->all();
            try {
                // do your database transaction here
                DB::beginTransaction();
                $user = User::create([
                    'fullname' => $data['user']['fullname'],
                    'username' => $data['user']['username'],
                    'email' => $data['user']['email'],
                    'level' => $data['user']['level'],
                    'password' => Hash::make($data['user']['password']),
                    'api_token' => str_random(60)
                ]);
                
                $tutor = Tutor::create([
                    'worker_number' => $data['tutor']['worker_number'],
                    'name' => $data['tutor']['name'],
                    'lastname' => $data['tutor']['lastname'],
                    'm_lastname' => $data['tutor']['m_lastname'],
                    'alt_email' => $data['tutor']['alt_email'],
                    'user_id' => $user->id
                ]);

                DB::commit();
    
                return response()->json(['tutor'=>$tutor, 'user'=>$user], 201);

                // return response()->json(['tutor'=>$data['tutor'], 'user'=>$data['user']], 201);

            } catch (\Illuminate\Database\QueryException $e) {
                // something went wrong with the transaction, rollback
                DB::rollback();
                return response()->json(['error'=>'Rollback transaction'], 500);
            } catch (\Exception $e) {
                // something went wrong elsewhere, handle gracefully
                return response()->json(['error' => $e], 500);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function updateTutor(Request $request, $id)
    {
        if ($request->isJson()) {
            $data = $request->json()->all();
            try {
                DB::beginTransaction();
                $tutor = Tutor::findOrFail($id);
                $user = User::findOrFail($tutor->user_id);

                $data = $request->json()->all();
                
                $tutor->worker_number = $data['tutor']['worker_number'];
                $tutor->name = $data['tutor']['name'];
                $tutor->lastname = $data['tutor']['lastname'];
                $tutor->m_lastname = $data['tutor']['m_lastname'];
                $tutor->alt_email = $data['tutor']['alt_email'];

                $user->fullname = $data['user']['fullname'];
                $user->username = $data['user']['username'];
                $user->email = $data['user']['email'];
                $user->access = $data['user']['access'];

                $tutor->save();
                $user->save();

                DB::commit();

                return response()->json(['tutor'=>$tutor, 'user'=>$user], 201);
            } catch (\Illuminate\Database\QueryException $e) {
                // something went wrong with the transaction, rollback
                DB::rollback();
                return response()->json(['error'=>'Rollback transaction'], 500);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'No content'], 406);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function deleteTutor(Request $request, $id)
    {
        if ($request->isJson()) {
            try {
                DB::beginTransaction();
                $tutor = Tutor::findOrFail($id);
                $user = User::findOrFail($tutor->user_id);

                $tutor->delete();
                $user->delete();
                DB::commit();

                return response()->json(['tutor'=>$tutor, 'user'=>$user], 200);
            } catch (\Illuminate\Database\QueryException $e) {
                // something went wrong with the transaction, rollback
                DB::rollback();
                return response()->json(['error'=>'Rollback transaction'], 500);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'No content'], 406);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function getTutorBy(Request $request, $key, $value)
    {
        if ($request->isJson()) {
            try {
                //if(Tutor::hasColumn($key)){
                    $tutorExist = Tutor::where($key, $value)->count();
                    if($tutorExist > 0){
                        $tutor = Tutor::where($key, $value)->get()[0];
                        $user = User::findOrFail($tutor->user_id);

                        return response()->json(['exist' => true,'tutor' => $tutor, 'user' => $user], 200);
                    }
                    else{
                        return response()->json(['error' => 'No content', 'exist' => false], 200);
                    }
                /*} else {
                    return response()->json(['error' => 'Bad parameters'], 406);
                }*/
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'No content', 'exist' => false], 200);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

}

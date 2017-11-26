<?php

namespace App\Http\Controllers;

use App\User;
use App\Student;
use App\Section;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StudentsController extends Controller
{
    function getAll(Request $request)
    {
        if ($request->isJson()) {
            $students = Student::with(['user','section'])->get();
            return response()->json($students, 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function getStudent(Request $request, $id)
    {
        if ($request->isJson()) {
            try {
                $student = Student::with(['user','section'])->findOrFail($id);

                return response()->json(['student'=>$student], 201);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'No content'], 406);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function getStudentByTerm(Request $request, $term, $limit = 5)
    {
        if ($request->isJson()) {
            if ($term !== '') {
                $students = Student::select('id','name','lastname','m_lastname')
                ->where('lastname', 'like', "$term%")
                ->orWhere('name', 'like', "$term%")
                ->limit($limit)->get();
                return response()->json($students, 200);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }
    
    function createStudent(Request $request)
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
                
                $student = Student::create([
                    'reg_number' => $data['student']['reg_number'],
                    'name' => $data['student']['name'],
                    'lastname' => $data['student']['lastname'],
                    'm_lastname' => $data['student']['m_lastname'],
                    'alt_email' => $data['student']['alt_email'],
                    'user_id' => $user->id,
                    'section_id' => $data['student']['section_id']
                ]);

                DB::commit();
    
                return response()->json(['student'=>$student, 'user'=>$user], 201);

                // return response()->json(['tutor'=>$data['tutor'], 'user'=>$data['user']], 201);

            } catch (\Illuminate\Database\QueryException $e) {
                // something went wrong with the transaction, rollback
                DB::rollback();
                return response()->json(['error'=>'Rollback transaction', 'detail'=>$e], 500);
            } catch (\Exception $e) {
                // something went wrong elsewhere, handle gracefully
                return response()->json(['error' => $e], 500);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function updateStudent(Request $request, $id)
    {
        if ($request->isJson()) {
            $data = $request->json()->all();
            try {
                DB::beginTransaction();
                $student = Student::findOrFail($id);
                $user = User::findOrFail($student->user_id);

                $data = $request->json()->all();
                
                // $student->reg_number = $data['student']['reg_number'];
                $student->name = $data['student']['name'];
                $student->lastname = $data['student']['lastname'];
                $student->m_lastname = $data['student']['m_lastname'];
                $student->alt_email = $data['student']['alt_email'];

                $user->fullname = $data['user']['fullname'];
                // $user->username = $data['user']['username'];
                // $user->email = $data['user']['email'];
                // $user->access = $data['user']['access'] ? $data['user']['access']: $user->access;

                $student->save();
                $user->save();

                DB::commit();

                return response()->json(['student'=>$student, 'user'=>$user], 201);
            } catch (\Illuminate\Database\QueryException $e) {
                // something went wrong with the transaction, rollback
                DB::rollback();
                return response()->json(['error'=>'Rollback transaction', 'detail'=>$e], 500);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'No content', 'detail'=>$e], 406);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function deleteStudent(Request $request, $id)
    {
        if ($request->isJson()) {
            try {
                DB::beginTransaction();
                $student = Student::with('user')->findOrFail($id);
                $user = User::findOrFail($student->user_id);

                $student->delete();
                $user->delete();
                DB::commit();

                return response()->json(['student'=>$student, 'user'=>$user], 200);
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

    function getStudentBy(Request $request, $key, $value)
    {
        if ($request->isJson()) {
            try {
                //if(Tutor::hasColumn($key)){
                    $studentExist = Student::where($key, $value)->count();
                    if($studentExist > 0){
                        $student = Student::where($key, $value)->with(['user','section'])->get();
                        // $user = User::findOrFail($student->user_id);

                        return response()->json(['exist' => true,'students' => $student], 200);
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

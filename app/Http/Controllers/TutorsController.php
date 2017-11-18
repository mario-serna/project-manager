<?php

namespace App\Http\Controllers;

use App\User;
use App\Tutor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TutorsController extends Controller
{
    function getAll(Request $request)
    {
        if ($request->isJson()) {
            $tutors = Tutor::all();
            return response()->json($tutors, 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function getTutor(Request $request, $id)
    {
        if ($request->isJson()) {
            try {
                $tutor = Tutor::findOrFail($id);

                return response()->json($tutor, 200);
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

            $tutor = Tutor::create([
                'name' => $data['name'],
                'lastname' => $data['lastname'],
                'm_lastname' => $data['m_lastname'],
                'gender' => $data['gender'],
                'user_id' => $data['user_id']
            ]);

            return response()->json($tutor, 201);
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function updateTutor(Request $request, $id)
    {
        if ($request->isJson()) {
            try {
                $tutor = Tutor::findOrFail($id);

                $data = $request->json()->all();
                
                $tutor->name = $data['name'];
                $tutor->lastname = $data['lastname'];
                $tutor->m_lastname = $data['m_lastname'];
                $tutor->gender = $data['gender'];

                $tutor->save();

                return response()->json($tutor, 200);
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
                $tutor = Tutor::findOrFail($id);

                $tutor->delete();

                return response()->json($tutor, 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'No content'], 406);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

}

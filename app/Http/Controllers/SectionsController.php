<?php

namespace App\Http\Controllers;

use App\Section;
use App\Tutor;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SectionsController extends Controller
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
            $section = Section::with('tutor')->get();
            return response()->json($section, 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function getSection(Request $request, $id)
    {
        if ($request->isJson()) {
            try {
                $section = Section::with('tutor')->findOrFail($id);

                return response()->json(['section'=>$section], 201);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'No content', 'detail'=>$e], 406);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function getSectionByTerm(Request $request, $term, $limit = 5)
    {
        if ($request->isJson()) {
            if ($term !== '') {
                $sections = Section::select('id','key','generation','carrer')
                ->where('key', 'like', "%$term%")->limit($limit)->get();
                return response()->json($sections, 200);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }
    
    function createSection(Request $request)
    {
        if ($request->isJson()) {
            $data = $request->json()->all();
            try {
                // do your database transaction here
                DB::beginTransaction();
                $section = Section::create([
                    'number' => $data['section']['number'],
                    'key' => $data['section']['key'],
                    'carrer' => $data['section']['carrer'],
                    'generation' => $data['section']['generation'],
                    'program' => $data['section']['program'],
                    'tutor_id' => $data['section']['tutor_id'],
                ]);

                DB::commit();
    
                return response()->json(['section'=>$section], 201);

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

    function updateSection(Request $request, $id)
    {
        if ($request->isJson()) {
            $data = $request->json()->all();
            try {
                DB::beginTransaction();
                $section = Section::findOrFail($id);

                $data = $request->json()->all();
                
                $section->number = $data['section']['number'];
                $section->key = $data['section']['key'];
                $section->carrer = $data['section']['carrer'];
                $section->generation = $data['section']['generation'];
                $section->program = $data['section']['program'];

                $section->save();

                DB::commit();

                return response()->json(['section'=>$section], 201);
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

    function deleteSection(Request $request, $id)
    {
        if ($request->isJson()) {
            try {
                DB::beginTransaction();
                $section = Section::findOrFail($id);

                $section->delete();
                DB::commit();

                return response()->json(['section'=>$section], 200);
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

    function getSectionBy(Request $request, $key, $value)
    {
        if ($request->isJson()) {
            try {
                //if(Tutor::hasColumn($key)){
                    $sectionExist = Section::where($key, $value)->count();
                    if($sectionExist > 0){
                        $section = Section::where($key, $value)->with('tutor')->get();

                        return response()->json(['exist' => true,'sections' => $section], 200);
                    }
                    else{
                        return response()->json(['error' => 'No content', 'exist' => false], 200);
                    }
                /*} else {
                    return response()->json(['error' => 'Bad parameters'], 406);
                }*/
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'No content', 'exist' => false, 'detail'=>$e], 200);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }
}

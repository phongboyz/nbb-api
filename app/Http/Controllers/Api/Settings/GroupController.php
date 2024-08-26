<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\GroupDetail;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    public function getData(Request $request){
        $validator = Validator::make($request->all(), [
            'qty'     => 'required|max:255'
          ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $data = Group::select('id','name')->where('name','like','%'.$request->search.'%')->orderBy('id','desc')->limit($request->qty)->get();
        return response()->json([
            'message'=>'success',
            'data'=>$data
        ],200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name'     => 'required|max:255'
          ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $data = Group::insert([
            'name'=>$request->name
        ]);

        return response()->json([
            'message'=>'success',
            'data'=>$data
        ],200);
    }

    public function edit(string $id){
        $data = Group::find($id);
        return response()->json([
            'message'=>'success',
            'data'=>$data
        ],200);
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'id'       => 'required',
            'name'     => 'required|max:255'
          ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $data = Group::where('id',$request->id)->update([
            'name'=>$request->name
        ]);
        return response()->json([
            'message'=>'success',
            'data'=>$data
        ],200);
    }

    public function destroy(string $id){
        $data = Group::where('id',$id)->delete();
        return response()->json([
            'message'=>'success',
            'data'=>$data
        ],200);
    }

}

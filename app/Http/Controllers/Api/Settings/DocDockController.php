<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SettingDoc\DocDock;
use Illuminate\Support\Facades\Validator;

class DocDockController extends Controller
{
    public function getData(Request $request){
        $validator = Validator::make($request->all(), [
            'qty'     => 'required|max:255'
          ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $data = DocDock::select('id','name')->where('name','like','%'.$request->search.'%')->orderBy('id','desc')->limit($request->qty)->get();
        return response()->json([
            'message'=>'success',
            'data'=>$data
        ],200);
    }
}

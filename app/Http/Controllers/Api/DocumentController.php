<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doc\DocumentIt;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    public function getDocIt(Request $request){
        $validator = Validator::make($request->all(), [
            'qty'     => 'required|max:255'
          ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $doc = DocumentIt::whereAny(['doc_no','no','doc_title'],'LIKE','%'.$request->search.'%')->limit($request->qty)->get();
        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }
}

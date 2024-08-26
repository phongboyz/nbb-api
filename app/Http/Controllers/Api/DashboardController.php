<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doc\DocumentIt;
use App\Models\DocType;

class DashboardController extends Controller
{
    public function getDashboard(){
        $doc = DocumentIt::selectRaw('tbl_doc_it.type_id as type_id')->selectRaw('count(tbl_doc_it.id) as count')->selectRaw('doc_types.name')->join('doc_types','tbl_doc_it.type_id','=','doc_types.id')->where('tbl_doc_it.depart_id',auth()->user()->dpart_id)->groupBy('tbl_doc_it.type_id')->get();
        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }

    public function getDocType(){
        $doc = DocType::select('id','name')->get();
        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }

    public function getDocTypeId(Request $request){
        $doc = DocType::select('id','name')->where('id', $request->type_id)->first();
        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }
}

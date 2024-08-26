<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\User;
use App\Models\Doc\DocumentIt;
use App\Models\Doc\LogDocument;
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

        $doc = DocumentIt::whereAny(['doc_no','no','doc_title'],'LIKE','%'.$request->search.'%')->where('depart_id',auth()->user()->dpart_id)->limit($request->qty)->get();
        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }

    public function getDocType(Request $request){
        $validator = Validator::make($request->all(), [
            'qty'     => 'required|max:255'
          ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $doc = DocumentIt::select('tbl_doc_it.*','b.name as groupname')->join('tbl_docgroupit as b','tbl_doc_it.docgroup_id','=','b.id')->whereAny(['doc_no','no','doc_title'],'LIKE','%'.$request->search.'%')->where('type_id',$request->type_id)->where('depart_id',auth()->user()->dpart_id)->orderBy('id','desc')->limit($request->qty)->get();
        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }



    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'file'     => 'required|max:20480000'
          ]);

            $fileName = date('ymdhis').'_'.$request->file->getClientOriginalName();
            $request->file->storeAs('upload/doc/'.date('dmy').'/', $fileName);
         
          $doc = new DocumentIt();
          $doc->doc_no = $request->doc_no;
          $doc->doc_date = $request->doc_date;
          $doc->doc_title = $request->doc_title;
          $doc->no = $request->no;
          $doc->date_no = $request->date_no;
          $doc->docgroup_id = $request->docgroup_id;
          $doc->dpart_id = $request->dpart_id;
          $doc->docdpart_id = $request->docdpart_id;
          $doc->sh_id = $request->sh_id;
          $doc->k_id = $request->k_id;
          $doc->type_id = $request->type_id;
          $doc->filename = $fileName;
          $doc->pathfile = 'upload/doc/'.date('dmy').'/'.$fileName;
          $doc->depart_id = $request->depart_id;
          $doc->user_id = $request->user_id;
          $doc->note = $request->note;
          $doc->datecreate = date('Y-m-d');
          $doc->save();

          $data = User::where('dpart_id',auth()->user()->dpart_id)->get();
          foreach($data as $item){
            $log = new LogDocument();
            $log->user_id = $item->id;
            $log->doc_id = $doc->id;
            $log->valuedt = date('Y-m-d H:i:s');
            $log->save();
          }

          return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }

    public function update(Request $request){

        if($request->file){
            $fileName = date('ymdhis').'_'.$request->file->getClientOriginalName();
            $request->file->storeAs('upload/doc/'.date('dmy').'/', $fileName);
        }
         
          $doc = DocumentIt::find($request->id);
          $doc->doc_no = $request->doc_no;
          $doc->doc_date = $request->doc_date;
          $doc->doc_title = $request->doc_title;
          $doc->no = $request->no;
          $doc->date_no = $request->date_no;
          $doc->docgroup_id = $request->docgroup_id;
          $doc->dpart_id = $request->dpart_id;
          $doc->docdpart_id = $request->docdpart_id;
          $doc->sh_id = $request->sh_id;
          $doc->k_id = $request->k_id;
          $doc->type_id = $request->type_id;

          if($request->file){
            $doc->filename = $fileName;
            $doc->pathfile = 'upload/doc/'.date('dmy').'/'.$fileName;
          }

          $doc->note = $request->note;
          $doc->save();

          return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }

    public function edit(string $id){
        $data = DocumentIt::find($id);
        $log = LogDocument::where('doc_id',$data->id)->where('user_id',auth()->user()->id)->update(['stt'=>0]);
        return response()->json([
            'message'=>'success',
            'data'=>$data
        ],200);
    }

    public function destroy(string $id){
        $data = DocumentIt::where('id',$id)->delete();
        return response()->json([
            'message'=>'success',
            'data'=>$data
        ],200);
    }

    public function getLogDoc(){
        $data = LogDocument::where('user_id',auth()->user()->id)->get();
        return response()->json([
            'message'=>'success',
            'data'=>$data
        ],200);
    }

    public function getTagDepart(Request $request){
        try {
            $tag = Tag::select('tbl_doc_it.*','b.name as groupname')->join('tbl_doc_it','tag_docs.docit_id','=','tbl_doc_it.id')->join('tbl_docgroupit as b','tbl_doc_it.docgroup_id','=','b.id')->where('tbl_doc_it.doc_title','like','%'.$request->search.'%')->where('tag_docs.department_id', auth()->user()->dpart_id)->limit($request->qty)->get();

            return response()->json([
                'message'=>'success',
                'data'=>$tag
            ],200);
        } catch (\Throwable $th) {
            $tag = Tag::select('tbl_doc_it.*','b.name as groupname')->join('tbl_doc_it','tag_docs.docit_id','=','tbl_doc_it.id')->join('tbl_docgroupit as b','tbl_doc_it.docgroup_id','=','b.id')->where('tbl_doc_it.doc_title','like','%'.$request->search.'%')->where('tag_docs.user_id', 0)->limit($request->qty)->get();
            return response()->json([
                'message'=>'error',
                'data'=>$tag
            ],200);
        }
        
    }

    public function getTagSector(Request $request){
        try {
            $tag = Tag::select('tbl_doc_it.*','b.name as groupname')->join('tbl_doc_it','tag_docs.docit_id','=','tbl_doc_it.id')->join('tbl_docgroupit as b','tbl_doc_it.docgroup_id','=','b.id')->where('tbl_doc_it.doc_title','like','%'.$request->search.'%')->where('tag_docs.sector_id', auth()->user()->sector_id)->limit($request->qty)->get();

            return response()->json([
                'message'=>'success',
                'data'=>$tag
            ],200);
        } catch (\Throwable $th) {
            $tag = Tag::select('tbl_doc_it.*','b.name as groupname')->join('tbl_doc_it','tag_docs.docit_id','=','tbl_doc_it.id')->join('tbl_docgroupit as b','tbl_doc_it.docgroup_id','=','b.id')->where('tbl_doc_it.doc_title','like','%'.$request->search.'%')->where('tag_docs.user_id', 0)->limit($request->qty)->get();
            return response()->json([
                'message'=>'error',
                'data'=>$tag
            ],200);
        }
        
    }

    public function getTagUser(Request $request){
        try {
            $tag = Tag::select('tbl_doc_it.*','b.name as groupname')->join('tbl_doc_it','tag_docs.docit_id','=','tbl_doc_it.id')->join('tbl_docgroupit as b','tbl_doc_it.docgroup_id','=','b.id')->where('tbl_doc_it.doc_title','like','%'.$request->search.'%')->where('tag_docs.user_id', auth()->user()->id)->limit($request->qty)->get();

            return response()->json([
                'message'=>'success',
                'data'=>$tag
            ],200);
        } catch (\Throwable $th) {
            $tag = Tag::select('tbl_doc_it.*','b.name as groupname')->join('tbl_doc_it','tag_docs.docit_id','=','tbl_doc_it.id')->join('tbl_docgroupit as b','tbl_doc_it.docgroup_id','=','b.id')->where('tbl_doc_it.doc_title','like','%'.$request->search.'%')->where('tag_docs.user_id', 0)->limit($request->qty)->get();
            return response()->json([
                'message'=>'error',
                'data'=>$tag
            ],200);
        }
        
    }

    public function storeTag(Request $request){
        $tag = new Tag();
        $tag->docit_id = $request->doc_id;
        $tag->department_id = $request->department_id;
        $tag->sector_id = $request->sector_id;
        $tag->user_id = $request->user_id;
        $tag->save();

        return response()->json([
            'message'=>'success',
            'data'=>$tag
        ],200);
    }
}

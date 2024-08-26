<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Docc;
use App\Models\LogDocc;

class DoccController extends Controller
{
    public function getDocc(Request $request){
        $validator = Validator::make($request->all(), [
            'qty'     => 'required|max:255'
          ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $doc = Docc::select('tbl_docc.*','departments.name')->join('departments','tbl_docc.dpart_id','=','departments.id')->whereAny(['tbl_docc.no','tbl_docc.title'],'LIKE','%'.$request->search.'%')->where('tbl_docc.dpart_id',auth()->user()->dpart_id)->orderBy('id','desc')->limit($request->qty)->get();
        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'docgroup_id'     => 'required',
            'no'     => 'required',
            'date'     => 'required',
            'title'     => 'required',
            'file'     => 'required',
          ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $fileName = date('ymdhis').'_'.$request->file->getClientOriginalName();
        $request->file->storeAs('upload/docc/'.date('dmy').'/', $fileName);

        $doc = new Docc();
        $doc->docgroup_id = $request->docgroup_id;
        $doc->no = $request->no;
        $doc->date = $request->date;
        $doc->title = $request->title;
        $doc->filename = $fileName;
        $doc->pathfile = 'upload/docc/'.date('dmy').'/'.$fileName;
        $doc->branch_id = auth()->user()->branch_id;
        $doc->dpart_id = auth()->user()->dpart_id;
        $doc->user_id = auth()->user()->id;
        $doc->datecreate = date('Y-m-d H:i:s');
        $doc->save();

        $data = User::get();
        foreach($data as $item){
          $log = new LogDocc();
          $log->docc_id = $doc->id;
          $log->user_id = $item->id;
          $log->create_data = date('Y-m-d H:i:s');
          $log->update_data = date('Y-m-d H:i:s');
          $log->save();
        }

        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }

    public function edit(string $id){
        $data = Docc::find($id);
        return response()->json([
            'message'=>'success',
            'data'=>$data
        ],200);
    }

    public function update(Request $request, int $id){
        $validator = Validator::make($request->all(), [
            'docgroup_id'     => 'required',
            'no'     => 'required',
            'date'     => 'required',
            'title'     => 'required',
          ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        if($request->file){
            $fileName = date('ymdhis').'_'.$request->file->getClientOriginalName();
            $request->file->storeAs('upload/docc/'.date('dmy').'/', $fileName);
        }
        

        $doc = Docc::find($id);
        $doc->docgroup_id = $request->docgroup_id;
        $doc->no = $request->no;
        $doc->date = $request->date;
        $doc->title = $request->title;

        if($request->file){
            $doc->filename = $fileName;
            $doc->pathfile = 'upload/docc/'.date('dmy').'/'.$fileName;
        }
        $doc->save();

        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }

    
    public function view(string $id){
        $data = Docc::find($id);
        $log = LogDocc::where('docc_id',$data->id)->where('user_id',auth()->user()->id)->first();
        if($log){
            $log->count += 1;
            $log->update_data = now();
            $log->del = 1;
            $log->save();
        }else{
            $new = new LogDocc();
            $new->docc_id = $id;
            $new->user = auth()->user()->id;
            $new->count = 1;
            $new->create_data = now();
            $new->update_data = now();
            $new->save();
        }
        
        return response()->json([
            'message'=>'success',
            'data'=>$data
        ],200);
    }

    public function destroy(int $id){
        $data = Docc::where('id',$id)->delete();
        $log = LogDocc::where('docc_id',$id)->delete();
        return response()->json([
            'message'=>'success',
            'data'=>$data
        ],200);
    }

    public function getLogDoccId(int $id){
        $data = LogDocc::select('log_doccs.count as count', 'log_doccs.create_data', 'log_doccs.update_data', 'log_doccs.del as del', 'tbl_docc.*', 'users.name as username')->join('tbl_docc','log_doccs.docc_id','=','tbl_docc.id')->join('users','log_doccs.user_id','=','users.id')->where('log_doccs.docc_id',$id)->get();
        return response()->json([
            'message'=>'success',
            'data'=>$data
        ],200);
    }

    public function getLogDocc(Request $request){
        $data = LogDocc::select('log_doccs.del as del', 'tbl_docc.*', 'departments.name as departname')->join('tbl_docc','log_doccs.docc_id','=','tbl_docc.id')->join('users','tbl_docc.user_id','=','users.id')->join('departments','users.dpart_id','=','departments.id')->where('log_doccs.user_id',auth()->user()->id)->whereAny(['tbl_docc.no','tbl_docc.title'],'LIKE','%'.$request->search.'%')->orderBy('tbl_docc.id','desc')->limit($request->qty)->get();
        return response()->json([
            'message'=>'success',
            'data'=>$data
        ],200);
    }

    public function getCountLog(){
        $data = LogDocc::selectRaw('count(id) as count')->where('user_id',auth()->user()->id)->where('del',0)->get();
        return response()->json([
            'message'=>'success',
            'data'=>$data
        ],200);
    }
}

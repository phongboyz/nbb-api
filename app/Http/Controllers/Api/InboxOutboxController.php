<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InboxOutbox;

class InboxOutboxController extends Controller
{
    public function getData(Request $request){
        $doc = InboxOutbox::select('messages.*','users.emp_name')->join('users','messages.user_id','=','users.id')->where('title','like','%'.$request->search.'%')->where('messages.receiver_id',auth()->user()->id)->where('messages.active_for_user',1)->orderBy('messages.id','desc')->get();
        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }

    public function getCountData(){
        $doc = InboxOutbox::selectRaw('count(messages.id) as count')->join('users','messages.user_id','=','users.id')->where('messages.receiver_id',auth()->user()->id)->where('messages.active_for_user',1)->where('messages.stt_msg',1)->orderBy('messages.id','desc')->get();
        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }

    public function sendData(Request $request){
        $doc = InboxOutbox::select('messages.*','users.emp_name')->join('users','messages.receiver_id','=','users.id')->where('messages.title','like','%'.$request->search.'%')->where('messages.user_id',auth()->user()->id)->where('messages.active',1)->orderBy('messages.id','desc')->get();
        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }

    public function bookDataUser(Request $request){
        $doc = InboxOutbox::select('messages.*','users.emp_name')->join('users','messages.receiver_id','=','users.id')->where('messages.title','like','%'.$request->search.'%')->where('messages.doc_type_for_user','LIK')->where('messages.receiver_id',auth()->user()->id)->where('messages.active_for_user',1)->orderBy('messages.id','desc')->get();
        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }

    public function bookData(Request $request){
        $doc = InboxOutbox::select('messages.*','users.emp_name')->join('users','messages.receiver_id','=','users.id')->where('messages.title','like','%'.$request->search.'%')->where('messages.doc_type','LIK')->where('messages.user_id',auth()->user()->id)->where('messages.active',1)->orderBy('messages.id','desc')->get();
        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'doc_title'     => 'required|max:255',
            'receive_id'     => 'required',
            'file'     => 'required',
          ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $fileName = date('ymdhis').'_'.$request->file->getClientOriginalName();
        $request->file->storeAs('upload/message/'.date('dmy').'/', $fileName);

                $message = new InboxOutbox();
                $message->date = date('Y-m-d');
                $message->title = $request->doc_title;
                $message->note = $request->description;
                $message->filename = $fileName;
                $message->pathfile = 'upload/message/'.date('dmy').'/'.$fileName;
                $message->receiver_id = $request->receive_id;
                $message->user_id = auth()->user()->id;
                $message->save();

                return response()->json([
                    'message'=>'success',
                    'data'=>$message
                ],200);
    }

    public function delDataUser(Request $request){
        $doc = InboxOutbox::select('messages.*','users.emp_name')->join('users','messages.user_id','=','users.id')->where('messages.title','like','%'.$request->search.'%')->where('messages.receive_id',auth()->user()->id)->where('messages.active_for_user',0)->orderBy('messages.id','desc')->get();
        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }

    public function delData(Request $request){
        $doc = InboxOutbox::select('messages.*','users.emp_name')->join('users','messages.receiver_id','=','users.id')->where('messages.title','like','%'.$request->search.'%')->where('messages.user_id',auth()->user()->id)->where('messages.active',0)->orderBy('messages.id','desc')->get();
        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }

    public function destroyUser(int $id){
        $doc = InboxOutbox::where('id',$id)->update(['active_for_user'=>0]);
        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }

    public function destroy(int $id){
        $doc = InboxOutbox::where('id',$id)->update(['active'=>0]);
        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }

    public function destroyData(int $id){
        $doc = InboxOutbox::where('id',$id)->delete();
        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }

    public function getDatafirst(int $id){
        $doc = InboxOutbox::select('messages.*','users.emp_name','users.img','users.email')->join('users','messages.user_id','=','users.id')->where('messages.id',$id)->first();
        $data = InboxOutbox::select('messages.*','users.emp_name')->join('users','messages.user_id','=','users.id')->where('messages.id',$id)->update(['messages.stt_msg'=>0]);
        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }

    public function postBookmaskUser(int $id){
        $doc = InboxOutbox::find($id); 
        if($doc['doc_type_for_user'] == 'NML'){
            $doc->doc_type_for_user = 'LIK';
            $doc->save();
        }else{
            $doc->doc_type_for_user = 'NML';
            $doc->save();
        }
        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }

    public function postBookmask(int $id){
        $doc = InboxOutbox::where('id',$id)->update(['doc_type'=>'LIK']);
        return response()->json([
            'message'=>'success',
            'data'=>$doc
        ],200);
    }

    public function downloadMessage(int $id){
        $doc = InboxOutbox::where('id',$id)->first();
        return response()->download(public_path($doc['pathfile']));
    }
}

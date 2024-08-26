<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mail;
use App\Models\User;
use App\Mail\TestMail;
use App\Mail\ResetPassMail;
use Illuminate\Support\Facades\Validator;


class MailController extends Controller
{
    public function sendMailWithAttachment(Request $request)
    {
        $mailData = [
            'title' => 'This is Test Mail',
            'files' => [
                public_path('attachments/test_image.jpeg'),
                public_path('attachments/test_pdf.pdf')
            ],
        ];
        Mail::to('to@gmail.com')->send(new TestMail($mailData));
             
        echo "Mail send successfully !!";
    }

    public function sent_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required|max:255'
          ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $data = User::where('email',$request->email)->first();

        $fourRandomDigit = mt_rand(1000,9999);
        $details = array(
            'code'=>$fourRandomDigit,
            'email'=>$data->email,
            'id'=>$data->id,
            'username'=>$data->name,
            'emp_name'=>$data->emp_name
        );
        \Mail::to($request->email, $data->emp_name)->send(new ResetPassMail($details));

        if($data){
            $user = User::find($data->id);
            $user->forgot_password = $fourRandomDigit;
            $user->save();
        }

        return response()->json([
            'message'=>'success',
            'data'=>$data
        ],200);
    }

    public function confirm_forgot(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'pass'     => 'required|max:255'
              ]);
            if ($validator->fails()) {
                return response()->json($validator->errors());
            }
    
            $data = User::find($request->id);
            if($data->forgot_password == $request->pass){
                $data->password = bcrypt($request->password);
                $data->save();
                return response()->json([
                    'message'=>'success',
                    'data'=>$data
                ],200);
            }else{
                return response()->json([
                    'message'=>'error',
                    'data'=>$data
                ],200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>'error'
            ],200);
        }
        
    }
}
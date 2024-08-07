<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\TestMail;
use App\Mail\ResetPassMail;
use Illuminate\Support\Facades\Validator;
use Mail;

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
            'mail'     => 'required|max:255'
          ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $fourRandomDigit = mt_rand(1000,9999);
        // $template_path = 'emails.email_template';
        //         $data = array(
        //             'code'=>$fourRandomDigit
        //         );
        // Mail::send($template_path,$data, function($message) {
        //     $message->to('phongsavanh@nbb.com.la', 'kk')->subject('ລະຫັດຢືນຢັນການລົງທະບຽນ');
        //     $message->from('dms_nbb@nbb.com.la','dms_nbb@nbb.com.la');
        // });
        // $template_path = 'email_forgot_password';
        $details = array(
            'code'=>$fourRandomDigit,
            // 'email'=>$data->email,
            // 'id'=>$data->id,
            // 'username'=>$data->name,
            // 'emp_name'=>$data->emp_name
        );
        \Mail::to($request->mail, $request->resent_name)->send(new ResetPassMail($details));
    }
}
<?php

namespace App\Http\Controllers\Api;

use Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\RegisMail;
use App\Models\Register;
use App\Models\User;
use App\Models\Sector;
use App\Models\Departments;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Auth\LoginResource;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required|string|max:255',
            'password'  => 'required|string'
          ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $credentials    =   $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'User not found'
            ], 401);
        }else{
            $user   = User::select('id','name','emp_name','phone','email','img','dpart_id','role_id')->where('email', $request->email)->firstOrFail();
            $token  = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'message'       => 'success',
                'user'          => $user,
                'access_token'  => $token,
                'token_type'    => 'Bearer'
            ], 200);

            // return LoginResource::make($user);
        }
    }

    public function logout(){
        Auth::user()->tokens()->delete();
        return response()->json([
            'message' => 'Logout successfull'
        ]);
    }

    public function allUser(Request $request){
        $data = User::select('users.*', 'departments.name as departname')->join('departments','users.dpart_id','=','departments.id')->where('users.name','like','%'.$request->search.'%')->get();
        return response()->json([
            'message'=>'success',
            'data'=>$data
        ],200);
    }

    public function getDpartUser(Request $request){
        $validator = Validator::make($request->all(), [
            'dpart_id'     => 'required',
          ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $data = User::where('dpart_id',$request->dpart_id)->orderBy('id','desc')->get();
        return response()->json([
            'message'=>'success',
            'data'=>$data
        ],200);
    }


    public function allDepartment(Request $request){
        $data = Departments::where('name','like','%'.$request->search.'%')->get();
        return response()->json([
            'message'=>'success',
            'data'=>$data
        ],200);
    }

    public function allSector(Request $request){
        $data = Sector::where('name','like','%'.$request->search.'%')->get();
        return response()->json([
            'message'=>'success',
            'data'=>$data
        ],200);
    }

    public function sectorByDpart(Request $request){
        $data = Sector::where('department_id', $request->department_id)->get();
        return response()->json([
            'message'=>'success',
            'data'=>$data
        ],200);
    }

    public function register(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'name'     => 'required',
                'emp_name'     => 'required',
                'email'     => 'required',
                'depart_id'     => 'required',
              ]);
            if ($validator->fails()) {
                return response()->json($validator->errors());
            }
            $fourRandomDigit = mt_rand(1000,9999);
    
            $regis = new Register();
            $regis->username = $request->name;
            $regis->user_emp_name= $request->emp_name;
            $regis->phone = $request->phone;
            $regis->email = $request->email;
            $regis->depart_id = $request->depart_id;
            $regis->sector_id = $request->sector_id;
            $regis->password = bcrypt($request->password);
            $regis->code = $fourRandomDigit;
            $regis->save();
    
            
            $details = array(
                'code'=>$fourRandomDigit,
                'email'=>$regis->email,
                'id'=>$regis->id,
                'username'=>$regis->username,
                'emp_name'=>$regis->user_emp_name
            );
            \Mail::to($regis->email, $regis->user_emp_name)->send(new RegisMail($details));
    
            return response()->json([
                'message'=>'success',
                'data'=>$regis
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>'error'
            ],200);
        }
        
    }

    public function regisFinish(Request $request){
        $validator = Validator::make($request->all(), [
            'code'     => 'required',
          ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $regis = Register::where('id',$request->id)->where('code', $request->code)->first();

        if($regis){
            $user = User::where('email',$regis->email)->first();
            if($user){
                return response()->json([
                    'message'=>'error'
                ],200);
            }else{
                $data = new User();
                $data->name = $regis->username;
                $data->emp_name = $regis->user_emp_name;
                $data->phone = $regis->phone;
                $data->email = $regis->email;
                $data->dpart_id = $regis->depart_id;
                $data->sector_id = $regis->sector_id;
                $data->password = $regis->password;
                $data->role_id = 1;
                $data->save();
    
                $regis->status = 0;
                $regis->save();
    
                return response()->json([
                    'message'=>'success',
                    'data'=>$data
                ],200);
            }
            
        }else{
            return response()->json([
                'message'=>'error'
            ],200);
        }
    }
}

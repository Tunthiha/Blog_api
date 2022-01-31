<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $attr = $request->validate([
            'name' => 'required|string',
            'email'=>'required|email|unique:users,email',
            'password'=> 'required|min:6|confirmed'
        ]);

        $user = User::create([
            'name'=>$attr['name'],
            'email'=>$attr['email'],
            'password'=>bcrypt($attr['password'])

        ]);
        return response([
            'user'=>$user,
            'token'=>$user->createToken('secret')->plainTextToken
        ]);

    }
    public function login(Request $request)
    {
        $attr = $request->validate([

            'email'=>'required|email',
            'password'=> 'required|min:6'
        ]);

 $credentials = $request->only('email', 'password');
      if(Auth::attempt($credentials))
      {
          $user = User::whereEmail($request->email)->first();

        return response([
            'user'=>$user,
            'token'=>$user->createToken('secret')->plainTextToken
        ],200);
      }
      return response([
        'message'=>'invalid creden'
    ],403);

    }
    public function logout(Request $request )
    {
        $request->user()->currentAccessToken()->delete();



        return response([
            'message'=>'logout success'
        ]);
    }
    public function user()
    {
        return response([
            'user'=>auth()->user()
        ],200);
    }
    public function update(Request $request)
    {
       $attr = $request->validate([
           'name'=> 'required|string'
       ]);
       $image = $this->saveImage($request->image,'profiles');

       $user = User::where('id',auth()->user()->id)->first();
       $user->update([
           'name'=>$attr['name'],
           'image'=>$image
       ]);
       return response([
        'message'=>'user updated',
        'user'=>auth()->user()
        ],200);
    }
}

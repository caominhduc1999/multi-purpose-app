<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Image;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        if (Gate::allows('isAdmin') || Gate::allows('isAuthor')){
            return User::latest()->paginate(1);
        }
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'name'  =>  'required',
            'email' =>  'required|email|unique:users',
            'type'  =>  'required',
            'password'=>'required',
            'bio'   =>  'required'
        ]);

        return User::create([
            'name'  =>  $request->name,
            'email'  =>  $request->email,
            'type'  =>  $request->type,
            'bio'  =>  $request->bio,
            'password'  =>  Hash::make($request->password)
        ]);

    }


    public function profile()
    {
        return auth('api')->user();
    }

    public function updateProfile(Request $request)
    {
        dd($request->all());
        $user = auth('api')->user();

        $currentPhoto = $user->photo;

        $this->validate($request, [
            'name'  =>  'required',
            'email' =>  'required|email|unique:users,email,'.$user->id.'',
            'password'=>'sometimes',
        ]);

        if ($request->photo != $currentPhoto){
            $name = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
            \Intervention\Image\Facades\Image::make($request->photo)->save(public_path('img/profile/').$name);

            $request->merge(['photo' => $name]);

            $userPhoto = public_path('img/profile/'.$currentPhoto);

            if (file_exists($userPhoto)){
                unlink($userPhoto);
            }

        }

        if(!empty($request->password)){
            $request->merge(['password' => Hash::make($request['password'])]);
        }

        $user->update($request->all());

        return $user;
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $this->validate($request, [
            'name'  =>  'required',
            'email' =>  'required|email|unique:users,email,'.$user->id.'',
            'type'  =>  'required',
            'password'=>'sometimes',
            'bio'   =>  'required'
        ]);
        
        $user->update($request->all());

        return [
            'message'=>'hi'
        ];
    }


    public function destroy($id)
    {
        $this->authorize('isAdmin');
        $user = User::findOrFail($id);
        $user->delete();
        return ['message'=>'delete'];
    }

    public function search(Request $request){
        if ($search = $request->q){
            $users = User::where(function ($query) use ($search){
                $query->where('name','LIKE','%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%')->orWhere('type','LIKE','%'.$search.'%');
            })->paginate(20);
        }else{
            $users =  User::latest()->paginate(5);
        }

        return $users;
    }
}

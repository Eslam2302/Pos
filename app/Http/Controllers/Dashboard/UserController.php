<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;



class UserController extends Controller
{

    public function __construct() 
    {
        
        $this->middleware(['permission:users_read'])->only('index');
        $this->middleware(['permission:users_create'])->only('create');
        $this->middleware(['permission:users_update'])->only('edit');
        $this->middleware(['permission:users_delete'])->only('destroy');


    }
    
    public function index(Request $request)
    {

        $users = User::whereRoleIs('admin')->where(function($q) use ($request) {

            return $q->when($request->search, function($query) use ($request){

                return $query->where('first_name', 'like', '%' . $request->search . '%')
                ->orwhere('last_name', 'like', '%' . $request->search . '%')
                ->orwhere('email', 'like', '%' . $request->search . '%');

            });

        })->latest()->paginate(5);
        

        return view('dashboard.users.index', compact('users'));
    } // end of index

    
    public function create()
    {

        return view('dashboard.users.create');

    } //end of create

    
    public function store(Request $request)
    {

        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email',
            'image' => 'image',
            'password' => 'required|confirmed',
            'permissions' => 'required',
        ]);

        $requests_data = $request->except(['password', 'password_confirmation', 'permissions', 'image']);
        $requests_data['password'] = bcrypt($request->password);

        if($request->image) {

            Image::make($request->image)->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path('uploads/user_images/' . $request->image->hashName()));

            $requests_data['image'] = $request->image->hashName(); 

        }


        $user = User::create($requests_data);
        $user->attachRole('admin');
        $user->syncPermissions($request->permissions);

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.users.index');

    } // end of store

    

    
    public function edit(User $user)
    {
        return view('dashboard.users.edit', compact('user'));

    } // end of edit

   
    public function update(Request $request, User $user)
    {
        
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'image' => 'image',
            'permissions' => 'required',
        ]);

        $requests_data = $request->except(['permissions', 'image']);

        if($request->image) {

            if($user->image != 'default.png') {

                storage::disk('public_uploads')->delete('/user_images/' . $user->image);
               
            }

            Image::make($request->image)->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path('uploads/user_images/' . $request->image->hashName()));

            $requests_data['image'] = $request->image->hashName(); 

        }

        $user->update($requests_data);

        $user->syncPermissions($request->permissions);

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.users.index');

    } //end of update

    public function destroy(User $user)
    {
        
        if($user->image != 'default.png') {

            storage::disk('public_uploads')->delete('/user_images/' . $user->image);

        }

        $user->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.users.index'); 

    } //end of destroy
}

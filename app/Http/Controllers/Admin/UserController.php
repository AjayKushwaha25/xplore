<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, Role};
use App\Http\Requests\{StoreAdminUserRequest, UpdateAdminUserRequest};
use Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            // 'counts' => $this->getCountsForSideNav(),
            'users' => User::get(),
        ];
        return view('admin.view.admin')->with('data',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            // 'counts' => $this->getCountsForSideNav(),
            'roles' => Role::pluck('name', 'id')
        ];
        return view('admin.add.admin')->with('data',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAdminUserRequest $request)
    {
        $data = [
            "name" => $request->validated('name'),
            "email" => $request->validated('email'),
            "password" => Hash::make($request->validated('password')),
            "status" => $request->validated('status'),
        ];
        $insert = User::create($data);

        $insert->roles()->attach($request->validated('role'));
        if(!$insert){
            exit(json_encode(array('status'=>'failed', 'message'=>'Something went wrong, please try again after sometime')));
        }
        echo json_encode(array('status'=>'success', 'message'=>'User Added successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $data = [
            // 'counts' => $this->getCountsForSideNav(),
            'userDetails' => $user,
            'roles' => Role::pluck('name', 'id')
        ];
        // dd($data['userDetails']);
        return view('admin.edits.admin')->with('data',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAdminUserRequest $request, User $user)
    {
        // dd($user->id);
        $data = [
            "name" => $request->validated('name'),
            "email" => $request->validated('email'),
            "password" => Hash::make($request->validated('password')),
            "status" => $request->validated('status'),
        ];
        $update = $user->update($data);
        $user->roles()->sync($request->validated('role'));
        if(!$update){
            exit(json_encode(array('status'=>'failed', 'message'=>'Something went wrong, please try again after sometime')));
        }
        echo json_encode(array('status'=>'success', 'message'=>'User updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}

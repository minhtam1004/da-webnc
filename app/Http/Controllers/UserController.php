<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\User as UserResource;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::all();
    }
    public function employeeIndex(Request $request)
    {       
        if(auth('api')->user()->roleId !== 1) return response()->json(['error'=>'do not have permission'],403); 
        if(!$request->limit) $request->merge(['limit',10]);
        if(!$request->page) $request->merge(['page',1]);
        if(!$request->keyword) $request->merge(['keyword','']);
        return User::where('roleId', 2)->where(function($query)use($request){
            $query->where('name','LIKE',"%{$request->keyword}%")
            ->orWhere('id','LIKE',"%{$request->keyword}%");
        })->get();
    }
    public function customerIndex(Request $request)
    {       
        if(auth('api')->user()->roleId > 2) return response()->json(['error'=>'do not have permission'],403); 
        if(!$request->limit) $request->merge(['limit',10]);
        if(!$request->page) $request->merge(['page',1]);
        if(!$request->keyword) $request->merge(['keyword','']);
        return User::where('roleId', 3)->where(function($query)use($request){
            $query->where('name','LIKE',"%{$request->keyword}%")
            ->orWhere('id','LIKE',"%{$request->keyword}%");
        })->get();
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return  response()->json(['name'=> $user->name, 'email' =>$user->email]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}

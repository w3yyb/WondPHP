<?php

namespace app\controllers;

use WondPHP\Request as Request;
// use Illuminate\Http\Request;

class PhotoCommentController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        echo 'photos  comments index';
    var_dump(route('photos.comments.index',['photo' => 123]));

        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        echo ' comments create';
    var_dump(route('photos.comments.create',['photo' => 234]));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        echo ' comments save';
    var_dump(route('photos.comments.save',['photo' => 456]));
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        echo 'comments show'.$id;
    var_dump(route('photos.comments.show',['id' => 100]));
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id,$haha)
    {
        echo 'comments edit'.$id.$haha;
    var_dump(route('photos.comments.edit',['comment' => 99,'photo' => 77]));
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        echo 'comments update'.$id;
    var_dump(route('photos.comments.update',['id' => 100]));
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        echo 'comments delete'.$id;
    var_dump(route('photos.comments.delete',['id' => 200]));
        
    }
}

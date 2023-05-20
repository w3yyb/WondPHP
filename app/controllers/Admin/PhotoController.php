<?php

namespace app\controllers\Admin;


use WondPHP\Request as Request;
// use Illuminate\Http\Request;

class PhotoController  
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        echo 'photos index';
    var_dump(route('admin.photos.index'));

        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        echo 'create';
    var_dump(route('admin.photos.create'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        echo 'save';
    var_dump(route('admin.photos.save'));
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        echo 'show'.$id;
    var_dump(route('admin.photos.show',['id' => 101]));
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        echo 'edit'.$id;
    var_dump(route('admin.photos.edit',['id' => 88]));
        
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
        echo 'update'.$id;
    var_dump(route('admin.photos.update',['id' => 100]));
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        echo 'delete'.$id;
    var_dump(route('admin.photos.delete',['id' => 200]));
        
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\ApiResponse;
class AttributeController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->success_response('Attribute deleted successfully',Attribute::all());
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,date,number,select',
            
        ]);

        if ($validator->fails()) {
            return $this->error_response('Create Error',$validator->errors());
        }
        $attr= Attribute::create([
            'name'=>$request->name,
            'type'=>$request->type,
            "slug"=>'s',
            'required'=>!empty($request->required),
            'unique'=>!empty($request->unique),
        ]);
        return $this->success_response('Attribute created successfully',$attr);
    }

    /**
     * Display the specified resource.
     */
    public function show(Attribute $attribute)
    {
        return $this->success_response('Attr retrieved success',$attribute);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attribute $attribute)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attribute $attribute)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:text,date,number,select',
        ]);

        $attribute->update([
            'name' => $request->name ,
            'type' => $request->type ,
            'slug' => 'sadas',
            'required' =>!empty($request->required),
            'unique' => !empty($request->unique),
        ]);
        return $this->success_response('Attribute updated successfully',$attribute);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attribute $attribute)
    {
        
        $attribute->delete();
        return $this->success_response('Attribute deleted successfully',$attribute);
    }
}

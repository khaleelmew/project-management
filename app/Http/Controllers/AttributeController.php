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
        $attributes=Attribute::query();
        if(!empty($request->entity_type)){
            $attributes->where('entity_type',$request->entity_type);
            //filtering for form type like project or in futre task. 
        }
        return $this->success_response('Attribute deleted successfully',$attributes->get());
        
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
        $request['entity_type']='project';
        $attr=Attribute::store_attribute($request);
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,date,number,select',
            
        ]);
        if ($validator->fails()) {
            return $this->error_response('Create Error',$validator->errors());
        }
        $request['entity_type']='project';
      
        $attribute=Attribute::store_attribute($request,$attribute->id);
        return $this->success_response('Attribute updated successfully',$attribute);
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attribute $attribute)
    {
       
        if(!empty($attribute->attr_values)){
            return $this->error_response('Delete error !, Attribute values exisit');
        }

        // $attribute->delete();
        return $this->success_response('Attribute deleted successfully',$attribute);
    }


}

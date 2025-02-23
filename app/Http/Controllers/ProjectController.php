<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Project;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use ApiResponse;
    public function index(Request $request)
    {
        $data=Project::query()->with(['attribute_values','users']);
       return $this->success_response('Projects list',$data->get());
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
            'users' => 'required|array|min:1',
            // 'status' => 'required|string',
            
        ]);
        if ($validator->fails()) {
            return $this->error_response('Create Error',$validator->errors());
        }
       
        // if(!empty($attributes->count())){
        //     return $this->error_response('These fields requried',$attributes->pluck('name'));
        // }
        $attributes_values=$request->attributes_values;

        // dd( collect($attributes_values)->pluck('attribute_id')->toArray());
        $rquired_attr=collect($attributes_values)->pluck('attribute_id')->toArray();
        if(!empty($rquired_attr)){
            $attributes=Attribute::whereIn('id',$rquired_attr)->get();
          
            $attributes=Attribute::where('required',true)->whereNotIn('id',$rquired_attr)->get();
            if(!empty($attributes->count())){
                return $this->error_response('These fields requried'.' '.implode(', ',$attributes->pluck('name')->toArray()));
            }
            
        }
        $project=Project::create([
            'name'=>$request->name,
            'status'=>$request->status??'pending',
            
        ]);
        
        $project->users()->sync($request->users??[]); 
        foreach($request->attributes_values as $attr_values){
            $attr_type=Attribute::find($attr_values['attribute_id']);
            $project->attribute_values()->create([
                'attribute_id'=>$attr_values['attribute_id'],
                'type'=>$attr_type->type,
                'value'=>$attr_values['value']
            ]);
         
        }

        return $this->success_response('Attribute updated successfully',$project);
        

    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return $this->success_response('Attr retrieved success',$project);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
    }
}

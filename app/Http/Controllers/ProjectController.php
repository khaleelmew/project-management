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
        $filters=$request->filters;
        $operators = ['=', '>', '<','>=','<=', 'LIKE','!='];
        $data=Project::with(['attribute_values.attribute','users']);
        if(!empty($filters)){
            foreach($filters as $key=> $filter){
                if(empty($filter['value'])||empty($filter['operator'])||!in_array($filter['operator'],$operators)) continue;
                if(in_array($key,['name','status'])){
                   
                    $data->where($key,$filter['operator'],$filter['value']);

                }else{
                    $attribute = Attribute::where('slug', $key)->first(); //changed to slug because name can be anything for showing purpose;
                    if ($attribute) {
                        $data->whereHas('attribute_values', function ($q) use ($filter,$attribute) {
                            $q->where('attribute_id', $attribute->id);
                            if($attribute->type=='date'){
                                $q->whereDate('value', $filter['operator'], $filter['value']);
                   
                            }else{
                                $q->where('value', $filter['operator'], $filter['operator'] === 'LIKE' ? "%".$filter['value']."%" : $filter['value']);
                   
                            }
                        });
                    }
                }
              
            }
        }   
        $data->orderBy('id','desc');
        if(!empty($request->paginate)){
            $data=$data->simplePaginate($request->limit??20);
        }else{
            $data=$data->get();
        }
       return $this->success_response('Projects list',$data);
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
       
     
        $data= Project::store_project($request);
        if(empty($data['status'])){
            return $this->error_response($data['msg']);
        }

        return $this->success_response('Attribute updated successfully',$data['data']);
        

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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'users' => 'required|array|min:1',
            
            
        ]);

        if ($validator->fails()) {
            return $this->error_response('Create Error',$validator->errors());
        }
       
     
        $data= Project::store_project($request,$project->id);
        if(empty($data['status'])){
            return $this->error_response($data['msg']);
        }

        return $this->success_response('Project updated successfully',$data['data']);
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {   
        $project->delete();
        return $this->success_response('Project deleted successfully',$project);
    }
}

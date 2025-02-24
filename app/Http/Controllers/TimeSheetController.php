<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Models\TimeSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TimeSheetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use ApiResponse;
    public function index(Request $request)
    {
        $time_sheets=TimeSheet::with(['user','project']);
        if(!empty($request->user_id)){
            $time_sheets->where('user_id',$request->user_id);
        }
        if(!empty($request->project_id)){
            $time_sheets->where('project_id',$request->project_id);
        }
        if(!empty($request->start_date)){
            $time_sheets->whereDate('sheet_date','>=',$request->start_date);
        }
        if(!empty($request->end_date)){
            $time_sheets->whereDate('sheet_date','<=',$request->end_date);
        }
        if(!empty($request->paginate)){
            $time_sheets=  $time_sheets->simplePaginate($request->limit);
        }
        if(!empty($request->is_sum)){
           $time_sheets= ['total_hours'=>$time_sheets->sum('hours')];
        }
        if(empty($request->paginate)&&empty($request->is_sum)){
            $time_sheets=$time_sheets->get();
        }
        return $this->success_response('Time Sheets',$time_sheets);

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
            'project_id' => 'required',
            'user_id' => 'required',
            'task_name' => 'required|string|max:255',
            'hours'=>'required|numeric',
            'sheet_date'=>'required|date'
            
            
        ]);

        if ($validator->fails()) {
            return $this->error_response('Create Error',$validator->errors());
        }
       
        TimeSheet::store_time_sheet($request);
        return $this->success_response('Time sheet created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(TimeSheet $timeSheet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TimeSheet $timeSheet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TimeSheet $timeSheet)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required',
            'user_id' => 'required',
            'task_name' => 'required|string|max:255',
            'hours'=>'required|numeric',
            'sheet_date'=>'required|date'
            
            
        ]);
        if ($validator->fails()) {
            return $this->error_response('Create Error',$validator->errors());
        }
        TimeSheet::store_time_sheet($request,$timeSheet->id);
        return $this->success_response('Time sheet updated successfully');
      
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimeSheet $timeSheet)
    {
        $timeSheet->delete();
        TimeSheet::update_project_total_hours($timeSheet->project_id);
        return $this->success_response('Time sheet deleted successfully');
    }
}

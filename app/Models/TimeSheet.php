<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSheet extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'user_id',
        'task_name',
        'sheet_date',
        'hours'
    ];
    public function user()
    {
       return $this->belongsTo(User::class);
    }
    public function project()
    {
       return $this->belongsTo(Project::class);
    }
    public static function store_time_sheet($request,$id=null){
        TimeSheet::updateOrCreate([
            'id'=>$id,
            
        ],[ 
            "task_name" => $request->task_name,
            "project_id" => $request->project_id,
            "user_id" =>$request->user_id,
            "sheet_date" => $request->sheet_date,
            "hours" => $request->hours
          ]);
          TimeSheet::update_project_total_hours($request->project_id); //for updating total time for the particular project so that can be shown in listing easily;
    }

    public static function update_project_total_hours($project_id){
        $total_work_hours=TimeSheet::where('project_id',$project_id)->sum('hours');
       
        $project=Project::where('id',$project_id)->update(['total_hours'=>$total_work_hours]);
      
    }
}

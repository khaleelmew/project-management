<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AttributeValue;
use Illuminate\Support\Facades\DB;
class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'status'
    ];
    protected static function booted()
    {
        static::deleting(function ($project) {

            $project->attribute_values()->delete();

            $project->users()->detach();
        });
    }
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function attribute_values()
    {
        return $this->morphMany(AttributeValue::class, 'entity');
    }



    public static function store_project($request, $id = null)
    {


        DB::beginTransaction();

        try {
            $attributes_values = $request->attributes_values;

            
            $rquired_attr = collect($attributes_values)->pluck('attribute_id')->toArray();
            if (!empty($rquired_attr)) {
                $attributes = Attribute::whereIn('id', $rquired_attr)->get();
                $available_attrs = $attributes->pluck('id')->toArray();
              
                $non_existing_attrs = array_diff($rquired_attr, $available_attrs);
                if (!empty($non_existing_attrs)) {
                    
                    // return ['status' => false, 'msg' => 'These attibutes are not defined' . ' ' . implode(', ', $non_existing_attrs)];
                }
                $attributes = Attribute::where('required', true)->whereNotIn('id', $rquired_attr)->get();
                if (!empty($attributes->count())) {
                    return ['status' => false, 'msg' => 'These fields requried' . ' ' . implode(', ', $attributes->pluck('name')->toArray())];
                    //requred attribute check, same way unique can be checked --pending
                }
            }
            $project = Project::updateOrCreate([
                'id' => $id
            ], [
                'name' => $request->name,
                'status' => $request->status ?? 'pending',

            ]);

            $project->users()->sync($request->users ?? []);
            foreach ($request->attributes_values as $attr_values) {
                $attr_type = Attribute::find($attr_values['attribute_id']);
                if(empty($attr_type)){
                    return ['status' => false, 'msg' => 'Attibute not defined '.$attr_values['attribute_id']];
                }
              
                $project->attribute_values()->updateOrCreate([
                    'attribute_id' => $attr_values['attribute_id'],
                    'entity_id' => $project->id,
                    'entity_type' => Project::class,
                ], [
                    'type' => $attr_type->type,
                    'value' => $attr_values['value'],
                ]);
            }

            DB::commit();
            return ['status' => true, 'data' => $project];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => false, 'msg' => $e->getMessage()];
         
        }
    }
    public function time_sheets()
    {
        return $this->hasMany(TimeSheet::class);
    }
}

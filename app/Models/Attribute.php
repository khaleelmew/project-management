<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = [
        'name',
        'type',
        "required",
        "unique",
        "slug",
        "entity_type"
    ];

    public static function store_attribute($request,$id=null){
      
       return  Attribute::updateOrCreate([
            'id'=>$id
        ],[
            'name'=>$request->name,
            'type'=>$request->type,
            'entity_type'=>$request->entity_type,
            "slug"=>str_replace('-', '_', \Str::slug($request->name)),
            'required'=>!empty($request->required), // making mandatory  for each entity
            'unique'=>!empty($request->unique), // making unique for each entity like LPO from customer
        ]);
    }
    public function attr_values(){
        return $this->hasMany(AttributeValue::class,'attribute_id');
    }
}

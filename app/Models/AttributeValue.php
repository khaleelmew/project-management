<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = ['attribute_id', 'entity_id','type', 'value','entity_type'];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class,'attribute_id');
    }
    public function entity()
    {
        return $this->morphTo(); 
    }

   
}

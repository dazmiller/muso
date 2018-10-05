<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    /**
     * Disabling the increment on the ID, we are using UUID
     *
     * @var boolean
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'original_name', 'path', 'content_type', 'size', 'duration'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['fileable_type','fileable_id'];

    /**
     * Get all of the owning fileable models.
     */
    public function fileable()
    {
        return $this->morphTo();
    }
}
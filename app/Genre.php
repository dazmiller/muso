<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Get the albums for this genre
     */
    public function albums()
    {
        return $this->hasMany('App\Album');
    }
}

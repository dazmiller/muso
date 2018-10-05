<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Activity extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['action', 'user_id','reference_type','reference_id'];

    /**
     * Get the author of this activity.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get all of the owning activity models.
     */
    public function activitable()
    {
        return $this->morphTo();
    }

    /**
     * Returns a list of total plays, likes, unlikes, download and comments
     * for the songs by month.
     */
    public function scopeStatistics($query, $from, $to) {
        return $query->select(DB::raw('YEAR(activities.created_at) as created_year, MONTH(activities.created_at) as created_month, activities.action, count(activities.action) as total'))
                    ->where('activities.reference_type', 'App\\Song')
                    ->whereBetween('activities.created_at', [$from, $to])
                    ->groupBy('activities.action', 'created_year', 'created_month')
                    ->orderBy('created_year', 'ASC')
                    ->orderBy('created_month', 'ASC');


    }
}

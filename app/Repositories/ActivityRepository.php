<?php

namespace App\Repositories;

use App\User;
use App\Album;
use App\Song;
use App\Activity;
use App\File;
use Config;
use DB;
use Log;

class ActivityRepository {

  /**
   * Returns only activities for the given user.
   */
  public function getLatestsByUser(User $user, $page=0){
    $pageSize = Config::get('app.page_size');

    $query = Activity::select(DB::raw('
                activities.id, activities.action, activities.created_at,
                activities.reference_type, activities.reference_id,
                files.id as file_id, files.name, files.path, files.public,
                users.id as user_id, users.name, users.image
            '))
            ->leftJoin('files','files.id','=','activities.reference_id')
            ->join('users', 'users.id', '=', 'activities.user_id')
            ->where('user_id',$user->id)
            ->where('activities.action', '<>' ,'play')
            ->where('activities.action', '<>' ,'download')
            ->where('activities.action', '<>' ,'follow')
            ->where('activities.action', '<>' ,'followed')
            ->where('activities.action', '<>' ,'unfollow')
            ->where('activities.action', '<>' ,'unfollowed')
            ->where('activities.action', '<>' ,'user-info')
            ->orderBy('created_at','DESC')
            ->take($pageSize);

    if ($page > 0) {
      $query->skip($page * $pageSize);
    }

    return $query->get();
  }

  public function countActivitiesByUser(User $user) {
    return Activity::where('user_id', $user->id)->count();
  }

  /**
   * Returns all activities for the users the given user follows.
   */
  public function getFeedForUser(User $user, $page=0) {
    $pageSize = Config::get('app.page_size');

    $query = Activity::select(DB::raw('
                activities.id, activities.action, activities.created_at,
                activities.reference_type, activities.reference_id,
                files.id as file_id, files.name as file_name, files.path as file_path, files.public as file_public,
                users.id as user_id, users.name, users.image
            '))
            ->join('followables','followables.followable_id','=','activities.user_id')
            ->leftJoin('files','files.id','=','activities.reference_id')
            ->join('users', 'users.id', '=', 'activities.user_id')
            ->where('followables.user_id',$user->id)
            ->where('followables.relation', '=' ,'follow')
            ->where('followables.followable_type', '=' ,'App\\User')
            ->where('activities.action', '<>' ,'play')
            ->where('activities.action', '<>' ,'download')
            ->where('activities.action', '<>' ,'follow')
            ->where('activities.action', '<>' ,'followed')
            ->where('activities.action', '<>' ,'unfollow')
            ->where('activities.action', '<>' ,'unfollowed')
            ->where('activities.action', '<>' ,'user-info')
            ->orderBy('activities.created_at','DESC')
            ->take($pageSize);
    
    if ($page > 0) {
      $query->skip($page * $pageSize);
    }

    return $query->get();
  }
}

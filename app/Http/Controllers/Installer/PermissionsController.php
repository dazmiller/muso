<?php

namespace App\Http\Controllers\Installer;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Config;
use Log;

class PermissionsController extends Controller
{
  
  private $folders = [
    'storage/framework/'     => '775',
    'storage/logs/'          => '775',
    'bootstrap/cache/'       => '775',
  ];

  public function check() {
    $results = [
      'permissions' => [],
      'errors' => null,
    ];

    foreach($this->folders as $folder => $permission)
    {
      $current = $this->getPermission($folder);
      if(!($current >= $permission))
      {
        array_push($results['permissions'], [
          'folder' => $folder,
          'permission' => $permission,
          'current' => $current,
          'isSet' => false,
        ]);
        $results['errors'] = true;
      }
      else {
        array_push($results['permissions'], [
          'folder' => $folder,
          'permission' => $permission,
          'current' => $current,
          'isSet' => true,
        ]);
      }
    }

    return response()->json($results);
  }

  /**
   * Returns the permissions for the giver folder
   */
  private function getPermission($folder) {
    return substr(sprintf('%o', fileperms(base_path($folder))), -4);
  }
}

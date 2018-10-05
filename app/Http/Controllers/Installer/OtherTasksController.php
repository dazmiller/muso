<?php

namespace App\Http\Controllers\Installer;

use Exception;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\BufferedOutput;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Http\Helpers\ConfigurationsHelper;
use Carbon\Carbon;
use App\Configuration;
use Validator;
use Config;
use Log;

class OtherTasksController extends Controller
{
  /**
   * Download the GeoIP database
   */
  public function geoip(Request $request) {
    
    try {
      // Get default service
      $service = app('geoip')->getService();

      // Ensure the selected service supports updating
      if (method_exists($service, 'update') === false) {
        return response()->json([
          "success" => false,
          "message" => 'The current service "' . get_class($service) . '" does not support updating.',
        ]);
      }

      // Perform update
      if ($result = $service->update()) {
        return response()->json([
          "success" => true,
          "message" => 'GeoIP downloaded successfully',
          "log"     => $result,
        ]);
      }

      return response()->json([
        "success" => true,
        "message" => 'Update failed!',
      ]);
    }
    catch(Exception $e){
      return response()->json([
        "success" => false,
        "message" => $e->getMessage(),
      ]);
    }
  }

  /**
   * Links the storage link to the public root folder
   */
  public function link(Request $request) {
    $outputLog = new BufferedOutput;

    try{
      if (!file_exists(base_path('public/storage'))) {
        Artisan::call('storage:link', [], $outputLog);
      }

      return response()->json([
        "success" => true,
        "message" => 'Successfully linked',
        "log"     => $outputLog->fetch(),
      ]);
    }
    catch(Exception $e){
      return response()->json([
        "success" => false,
        "message" => $e->getMessage(),
      ]);
    }
  }

  /**
   * Add configurations to database and updates the public/dist/index.html file
   */
  public function configs(Request $request) {
    try {
      $configurations = $request->input('configurations');
        
      foreach ($configurations as $index => $config) {
        $saved = Configuration::where('key', $config['key'])->first();

        if ($saved) {
          $saved->fill($config);
          $saved->save();
        } else {
          $saved = Configuration::create($config);
        }
      }

      // Replace configs in html file
      $configurations = Configuration::all();
      ConfigurationsHelper::updateIndexHtml($configurations);
      
      return response()->json([
        "success" => true,
        "message" => 'Successfully created',
        "log"     => 'Successfully created',
      ]);
    } catch(Exception $e) {
      return response()->json([
        "success" => false,
        "message" => $e->getMessage(),
        "log"     => $e->getMessage(),
      ], 500);
    }
  }

  /**
   * Removes the installer folder from the public root folder,
   * after all the installation is completed.
   */
  public function remove(Request $request) {
    $success = true;

    // Removing the installer!
    if (file_exists(base_path('public/installer'))) {
      $success = File::deleteDirectory(base_path('public/installer'));
    }

    // Create lock file to lock the installer actions
    $bytes_written = File::put(base_path('storage/app/installed.lock'), json_encode([
      "success" => true,
      "time" => Carbon::now(),
    ]));

    if ($bytes_written === false) {
      return response()->json([
        "success" => false,
        "message" => 'Lock file was not created!! Please make sure to manually create this file: `storage/app/installed.lock`',
      ]);
    }

    if ($success) {
      return response()->json([
        "success" => true,
        "message" => 'Successfully removed the installer',
      ]);
    }
   
    return response()->json([
      "success" => false,
      "message" => 'Installer not removed, please manually remove the folder: `public/installer`',
    ]);
  }
}

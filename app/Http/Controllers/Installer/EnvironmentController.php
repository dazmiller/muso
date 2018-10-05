<?php

namespace App\Http\Controllers\Installer;

use Exception;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\BufferedOutput;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use Config;
use Log;

class EnvironmentController extends Controller
{

  private $envPath;
  private $envExamplePath;
  private $validations = [
    'ENV_CONTENT'      => 'required',
  ];

  /**
   * Set the .env and .env.example paths.
   */
  public function __construct()
  {
      $this->envPath = base_path('.env');
      $this->envExamplePath = base_path('.env.example');
  }

  /**
   * Saves the .env file with user's input
   */
  public function store(Request $request) {
    $validator = Validator::make($request->all(), $this->validations, [
      'ENV_CONTENT.required' => 'The ENV_CONTENT is required',
    ]);

    if ($validator->fails()) {
      return response()->json([
          'success'=> false,
          'message' => $validator->errors()->all(),
      ], 400);
    }

    try {
      file_put_contents($this->envPath, $request->input('ENV_CONTENT'));

      // Reloading new configurations
      $outputLog = new BufferedOutput;
      Artisan::call('config:clear', [], $outputLog);

      return response()->json([
        "success" => true,
        "message" => ".env file created",
        "log"     => $outputLog->fetch(),
      ]);
    }
    catch(Exception $e) {
      return response()->json([
        "success" => false,
        "message" => $e->getMessage(),
      ]);
    }
  }

  /**
   * Generates a new APP_KEY and save it into the .env file
   */
  public function appkey(Request $request) {
    $outputLog = new BufferedOutput;

    try{
      Artisan::call('key:generate', ["--force"=> true], $outputLog);

      // Reloading new configurations
      $outputLog = new BufferedOutput;
      Artisan::call('config:clear', [], $outputLog);

      return response()->json([
        "success" => true,
        "message" => 'APP_KEY generated successfully',
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
   * Generates a new JWT_SECRET and save it into the .env file
   */
  public function jwtkey(Request $request) {
    
    try {
      $outputLog = new BufferedOutput;
      $key = Str::random(32);

      if (Str::contains(file_get_contents($this->envPath), 'JWT_SECRET') === false) {
          // If entry doesn't exist append to the file
          file_put_contents($this->envPath, PHP_EOL."JWT_SECRET=$key", FILE_APPEND);
      } else {
          // Update existing JWT_SECRET
          file_put_contents($this->envPath, str_replace(
              'JWT_SECRET=',
              'JWT_SECRET='.$key, file_get_contents($this->envPath)
          ));
      }

      // Reloading new configurations
      $outputLog = new BufferedOutput;
      Artisan::call('config:clear', [], $outputLog);
      
      return response()->json([
        "success" => true,
        "message" => 'JWT_SECRET successfully added',
      ]);
    }
    catch(Exception $e){
      return response()->json([
        "success" => false,
        "message" => $e->getMessage(),
      ]);
    }
  }
}

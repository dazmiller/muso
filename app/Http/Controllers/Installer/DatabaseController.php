<?php

namespace App\Http\Controllers\Installer;

use Exception;
use Illuminate\Http\Request;
use App\Http\Requests;
use Symfony\Component\Console\Output\BufferedOutput;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Controller;
use App\User;
use URL;
use Validator;
use Config;
use Log;

class DatabaseController extends Controller
{

  private $validations = [
    'DB_HOST'      => 'required',
    'DB_USERNAME'  => 'required',
    'DB_DATABASE'  => 'required',
  ];

  public function check(Request $request) {
    $validator = Validator::make($request->all(), $this->validations, [
      'DB_HOST.required' => 'The DB_HOST is required',
      'DB_USERNAME.required' => 'The DB_USERNAME is required',
      'DB_DATABASE.required' => 'The DB_DATABASE is required',
    ]);

    if ($validator->fails()) {
      return response()->json([
          'success'=> false,
          'message' => $validator->errors()->all(),
      ],400);
    }

    $conn = @mysqli_connect(
      $request->input('DB_HOST'),
      $request->input('DB_USERNAME'),
      $request->input('DB_PASSWORD'),
      $request->input('DB_DATABASE')
    );

    if (mysqli_connect_errno()) {
      return response()->json([
        "success" => false,
        "message" => mysqli_connect_error(),
      ]);
    }

    mysqli_close($conn);

    return response()->json([
      "success" => true,
      "message" => "Connected successfully",
    ]);
  }

  /**
   * Run the migrations!
   */
  public function migrate(Request $request) {
    $outputLog = new BufferedOutput;

    try{
      $new_connection = 'temporal';

      config(["database.connections.$new_connection" => [
        "driver" => "mysql",
        "host" => $request->input('DB_HOST'),
        "port" => $request->input('DB_PORT'),
        "database" => $request->input('DB_DATABASE'),
        "username" => $request->input('DB_USERNAME'),
        "password" => $request->input('DB_PASSWORD'),
        "charset" => "utf8",
        "collation" => "utf8_unicode_ci",
        "prefix" => "",
        "strict" => true,
        "engine" => null
      ]]);

      Artisan::call('migrate', ["--force"=> true, '--database' => $new_connection], $outputLog);

      return response()->json([
        "success" => true,
        "message" => 'Successfully migrated',
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

  public function admin(Request $request) {
    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'email' => 'required',
      'password' => 'required',
    ]);

    if ($validator->fails()) {
      return response()->json([
          'success'=> false,
          'message' => $validator->errors()->all(),
      ], 400);
    }

    $default = URL::to('/')."/images/avatar.png";
    $gravatar = "https://www.gravatar.com/avatar/" . md5( strtolower( trim( $request->input('email') ) ) ) . "?d=" . urlencode( $default ) . "&s=400";

    $user = new User();
    $user->fill($request->all());
    $user->email = $request->input('email');
    $user->password = bcrypt($request->input('password'));
    $user->image = $gravatar;
    $user->admin = true;
    $user->author = true;
    $user->save();

    return response()->json([
          'success'=> true,
          'message' => 'User successfully created',
    ]);
  }
}

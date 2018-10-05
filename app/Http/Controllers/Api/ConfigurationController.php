<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Helpers\ConfigurationsHelper;
use App\Http\Controllers\Controller;
use App\Configuration;
use Log;
use Gate;
use JWTAuth;
use Validator;

class ConfigurationController extends Controller
{

  /**
   * Create a new controller instance.
   *
   * @param  TaskRepository  $tasks
   * @return void
   */
  public function __construct()
  {
    $this->validations = [
      'configurations'          => 'required',
      'configurations.*.key'    => 'required|min:3|max:255',
      'configurations.*.value'  => 'required',
    ];
  }

  /**
   * Returns all configurations for the system
   */
  public function index() {
    $user = JWTAuth::parseToken()->authenticate();

    if (Gate::forUser($user)->allows('config-index', null)) {
      $configurations = Configuration::all();

      return response()->json([
        'success'   => true,
        'configurations' => $configurations,
      ]);
    }

    return response()->json([
      'success'   => false,
      'errors'    => ['Only authors can access this resource.']
    ], 403);
  }

  /**
   * Creates (or updates) a new configuration record in the database,
   * this action receives an array of configurations, then loops and saves/updates
   * each of the configurations.
   */
  public function store(Request $request) {
    $isDemo = env('APP_IS_DEMO', false);

    if ($isDemo) {
      return response()->json([
        'success'   => false,
        'errors'    => ['This feature is disabled for the demo.']
      ], 403);
    }

    $user   = JWTAuth::parseToken()->authenticate();

    if (Gate::forUser($user)->allows('config-create', null)) {
      $validator = Validator::make($request->all(), $this->validations);

      if ($validator->fails()) {
        return response()->json([
          'success'=> false,
          'errors' => $validator->errors()->all()
        ], 400);
      }

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
      $ga = Configuration::where('key', 'APP_GA_ID')->first();

      $whitelist = [
        'APP_TITLE', 'APP_IS_NEW_USER_AUTHOR', 'APP_GA_ID', 'APP_GA_ENABLED',
        'APP_FACEBOOK_ENABLED', 'APP_FACEBOOK_APP_ID', 'APP_DOWNLOAD_SONG_FILE'
      ];
      $configurations = collect($configurations)->filter(function($value) use ($whitelist) {
        return in_array($value->key, $whitelist);
      });

      if (isset($ga)) {
        $ga = $ga->value;
      }

      ConfigurationsHelper::updateIndexHtml($configurations);
      ConfigurationsHelper::insertGACode($ga);

      return response()->json([
        'success'=> true,
        'message' => 'Configuration successfully saved.',
      ]);
    }

    return response()->json([
      'success'   => false,
      'errors'    => ['Only admins can access this resource.']
    ], 403);
  }

  /**
   * Action that receives the css to apply to the main html file.
   */
  public function apply(Request $request) {
    $isDemo = env('APP_IS_DEMO', false);

    if ($isDemo) {
      return response()->json([
        'success'   => false,
        'errors'    => ['This feature is disabled for the demo.']
      ], 403);
    }

    $user   = JWTAuth::parseToken()->authenticate();

    if (Gate::forUser($user)->allows('config-styles', null)) {
      $css = $request->input('css');

      ConfigurationsHelper::insertCssCode($css);

      return response()->json([
        'success'=> true,
        'message' => 'Theme successfully applied!',
      ]);
    }

    return response()->json([
      'success'   => false,
      'errors'    => ['Only admins can access this resource.']
    ], 403);
  }

  /**
   * Action that clears the current theme
   */
  public function clear(Request $request) {
    $isDemo = env('APP_IS_DEMO', false);

    if ($isDemo) {
      return response()->json([
        'success'   => false,
        'errors'    => ['This feature is disabled for the demo.']
      ], 403);
    }

    $user   = JWTAuth::parseToken()->authenticate();

    if (Gate::forUser($user)->allows('config-styles', null)) {
      $theme = Configuration::where('key', 'APP_CURRENT_THEME')->first();

      if ($theme) {
        $theme->value = '{}';
        $theme->save();
      }

      ConfigurationsHelper::clearCssCode();

      return response()->json([
        'success'=> true,
        'message' => 'Theme successfully cleared!',
      ]);
    }

    return response()->json([
      'success'   => false,
      'errors'    => ['Only admins can access this resource.']
    ], 403);
  }
}
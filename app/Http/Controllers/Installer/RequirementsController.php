<?php

namespace App\Http\Controllers\Installer;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Config;
use Log;

class RequirementsController extends Controller
{
  private $_minPhpVersion = '7.1.0';
  private $requirements = [
    'php' => [
      'openssl',
      'pdo',
      'mbstring',
      'tokenizer',
      'JSON',
      'cURL',
      'mysqli',
    ],
    'apache' => [
      'mod_rewrite',
    ],
  ];

  public function check() {
    $results = [];

    foreach($this->requirements as $type => $requirement)
    {
      switch ($type) {
        // check php requirements
        case 'php':
          foreach($this->requirements[$type] as $requirement)
          {
            $results['requirements'][$type][$requirement] = true;
            if(!extension_loaded($requirement))
            {
              $results['requirements'][$type][$requirement] = false;
              $results['errors'] = true;
            }
          }
          break;
        // check apache requirements
        case 'apache':
          foreach ($this->requirements[$type] as $requirement) {
            // if function doesn't exist we can't check apache modules
            if(function_exists('apache_get_modules'))
            {
              $results['requirements'][$type][$requirement] = true;
              if(!in_array($requirement,apache_get_modules()))
              {
                $results['requirements'][$type][$requirement] = false;
                $results['errors'] = true;
              }
            }
          }
          break;
      }
    }

    $minVersionPhp = $this->_minPhpVersion;
    $currentPhpVersion = $this->getPhpVersionInfo();
    $supported = false;
    if (version_compare($currentPhpVersion['version'], $minVersionPhp) >= 0) {
      $supported = true;
    }
    $results['php'] = [
        'full' => $currentPhpVersion['full'],
        'current' => $currentPhpVersion['version'],
        'minimum' => $minVersionPhp,
        'supported' => $supported
    ];

    return response()->json($results);
  }

  /**
   * Get the current PHP version
   */
  private function getPhpVersionInfo() {
    $currentVersionFull = PHP_VERSION;

    preg_match("#^\d+(\.\d+)*#", $currentVersionFull, $filtered);
    $currentVersion = $filtered[0];
    
    return [
      'full' => $currentVersionFull,
      'version' => $currentVersion
    ];
  }
}
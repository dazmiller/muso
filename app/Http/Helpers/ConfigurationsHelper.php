<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Log;

class ConfigurationsHelper {

  /**
   * Updates the public/dist/index.html file with the given configurations.
   */
  public static function updateIndexHtml($configurations) {
    $index = public_path() . '/dist/index.html';

    if (\File::exists($index)) {
      // Replace configurations
      $code = self::getJSCode($configurations);
      $pattern = '/<script type=text\/javascript id=configurations>.*<\/script>/';
      $replacement = "<script type=text/javascript id=configurations>$code</script>";

      $html = \File::get($index);
      $html = preg_replace($pattern, $replacement, $html);

      // Replace title
      $title = self::getTitle($configurations);
      $pattern = '/<title>.*<\/title>/';
      $replacement = "<title>$title</title>";
      $html = preg_replace($pattern, $replacement, $html);

      $bytes_written = \File::put($index, $html);
      if ($bytes_written === false) {
        throw new \Exception("Configurations not updated correctly, please make sure you have write access in your server.");
      }
    }
  }

  private static function getJSCode($configurations = []) {
    $total = count($configurations) - 1;
    $code = "var Configurations = {";
    foreach ($configurations as $index => $config) {
      if ($config->value == '1' || $config->value == '0') {
        // Boolean values
        $code .= $config->key.': '.($config->value == '1' ? 'true' : 'false');
      } else {
        // everything else is a string
        $code .= $config->key.': "'.htmlspecialchars($config->value).'"';
      }

      if ($index < $total) {
        $code .= ',';
      }
    }
    $code .= "};";
    return $code;
  }

  private static function getTitle($configurations) {
    foreach ($configurations as $key => $config) {
      if ($config->key == 'APP_TITLE') {
        return htmlspecialchars($config->value);
      }
    }

    return config('app.title');
  }
  

  /**
   * Insert GA code if enabled
   */
  public static function insertGACode($gaCode = 'UNDEFINED') {

    $index = public_path() . '/dist/index.html';

    if (\File::exists($index)) {
      // Replace configurations
      $pattern = '/<script async src="(.)*" id=gtm><\/script>/';
      $replacement = "<script async src=\"https://www.googletagmanager.com/gtag/js?id=$gaCode\" id=gtm></script>";

      $html = \File::get($index);
      $html = preg_replace($pattern, $replacement, $html);

      $bytes_written = \File::put($index, $html);
      if ($bytes_written === false) {
        throw new \Exception("Configurations not updated correctly, please make sure you have write access in your server.");
      }
    }
  }

  /**
   * Insert the CSS Styles into the html page
   */
  public static function insertCssCode($css) {
    $index = public_path() . '/dist/index.html';

    if (\File::exists($index)) {
      // Replace configurations
      $content = preg_replace('/\r|\n/', '', $css);
      $styleSheetFileName = "theme.".hash('ripemd160', $content).".css";
      
      $pattern = '/<link rel=stylesheet id=selected-theme( href="(.)*")?>/';
      $replacement = "<link rel=stylesheet id=selected-theme href=\"/dist/$styleSheetFileName\">";

      $html = \File::get($index);
      $html = preg_replace($pattern, $replacement, $html);

      $bytes_written = \File::put($index, $html);
      $cssBytes_written = \File::put(public_path()."/dist/$styleSheetFileName", $content);
      if ($bytes_written == false || $cssBytes_written == false) {
        throw new \Exception("Styles not updated correctly! Please make sure you have write access in your server.");
      }
    }
  }
  
  /**
   * Clear the current CSS Styles in the html page
   */
  public static function clearCssCode() {
    $index = public_path() . '/dist/index.html';

    if (\File::exists($index)) {
      // Remote stylesheet
      $pattern = '/<link rel=stylesheet id=selected-theme href="(.)*">/';
      $replacement = "<link rel=stylesheet id=selected-theme>";

      $html = \File::get($index);
      $html = preg_replace($pattern, $replacement, $html);

      $bytes_written = \File::put($index, $html);

      if ($bytes_written == false) {
        throw new \Exception("Styles not updated correctly! Please make sure you have write access in your server.");
      }
    }
  }
}

<?php

$minPhpVersion = '7.1.0';
$currentPhpVersion = getPhpVersionInfo();
$supported = false;

if (version_compare($currentPhpVersion['version'], $minPhpVersion) >= 0) {
  $supported = true;
}

function getPhpVersionInfo() {
  $currentVersionFull = PHP_VERSION;

  preg_match("#^\d+(\.\d+)*#", $currentVersionFull, $filtered);
  $currentVersion = $filtered[0];

  return [
    'full' => $currentVersionFull,
    'version' => $currentVersion
  ];
}

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Installer</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <script>
    window.env = {
      supported: <?php echo $supported ? 'true' : 'false'; ?>,
      php: <?php echo json_encode($currentPhpVersion); ?>,
   };
  </script>
</head>
<body>
  <div id="root" class="main">
  </div>
</body>
</html>

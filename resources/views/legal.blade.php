
<!DOCTYPE html>
<html>
<head>
  <title>{{$title}}</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <style type="text/css">
  .container {
    max-width: 800px;
    margin: 0 auto;
    font-family: Verdana;
    padding: 0 20px;
  }

  .container h2,
  .container h3,
  .container h4 {
    margin-bottom: 5px;;
    font-weight: normal;
  }

  .container p {
    margin-top: 0;
    font-size: 16px;
    line-height: 1.7;
  }
  </style>
</head>
<body>
<div class="container">  
<h1>{{$title}}</h1>
<p>Last updated: {{Carbon\Carbon::parse($legal->updated_at)->format('l jS, F Y')}}</p>
{!! $legal->value !!}
</div>
</body>
</html>

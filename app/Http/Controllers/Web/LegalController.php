<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Configuration;
use Log;

class LegalController extends Controller
{

  public function terms(Request $request) {
    $legal = Configuration::where('key', 'APP_LEGAL_TERMS')->first();

    if ($legal) {
      return view('legal', ['title' => 'Terms & Conditions', 'legal' => $legal]);
    }

    abort(404);
  }

  public function privacy(Request $request) {
    $legal = Configuration::where('key', 'APP_LEGAL_PRIVACY')->first();
    
    if ($legal) {
      return view('legal', ['title' => 'Private Policy', 'legal' => $legal]);
    }

    abort(404);
  }

}

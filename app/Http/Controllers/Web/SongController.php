<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Serializers\SongSerializer;
use App\Repositories\SongRepository;
use App\Song;
use Log;

class SongController extends Controller
{

  public function __construct(SongRepository $songRepository, SongSerializer $songSerializer)
  {

    $this->songRepository = $songRepository;
    $this->songSerializer = $songSerializer;
  }

  public function show(Request $request, $id) {
    $index = public_path() . '/dist/index.html';

    if (File::exists($index)) {
      $record = $this->songRepository->findPublished($id);

      if($record) {
        $song = $this->songSerializer->one($record, ['basic', 'full']);
        $html = File::get($index);
        $html = str_replace($this->getTagToReplace(), $this->getTagsReplacement($song), $html);

        return $html;
      }

      return abort(404);
    }

    return view('welcome');
  }

  private function getTagToReplace() {
    return '<meta property=replace content=og:tags>';
  }
  
  private function getTagsReplacement($song) {
    $hash = "#!/public/songs/".$song['id'];
    $url = $hash;
    $title = $song['title'];
    $description = $song['description'];
    $image = $song['album']['image'];

    return (
      "<meta property=\"og:title\" content=\"$title\" />"
      ."<meta property=\"og:type\" content=\"website\" />"
      ."<meta property=\"og:description\" content=\"$description\" />"
      ."<meta property=\"og:image\" content=\"$image\" />"
      ."<meta property=\"og:url\" content=\"$url\" />"
      ."<script type=\"text/javascript\">window.location.hash = '$hash';</script>"
    );
  }
}

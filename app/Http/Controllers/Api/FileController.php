<?php

namespace App\Http\Controllers\Api;

use hisorange\BrowserDetect\Facade as Browser;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\Request;
use Storage;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\FileRepository;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use Validator;
use Config;
use App\File as Fileentry;
use File;
use App\Song;
use App\Configuration;
use App\Activity;
use Response;
use Log;

class FileController extends Controller
{


    /**
     * Display the specified resource from file system or S3
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $disk = Config::get('filesystems.default');
        $file = Fileentry::find($id);
        $redirectToFile = false;

        if($file){
            $browser = Browser::browserFamily();

            if ($disk == 'local') {
                $path = storage_path() . '/app/public/' . $file->path . '/' . $file->name;

                // We need to stream the file for chrome to work correctly with the player.
                if ($browser == 'Chrome') {
                    $response = new BinaryFileResponse($path);
                } else {
                    $f = File::get($path);
                    $response = Response::make($f, 200);
                    $response->header("Content-Type", $file->content_type);

                    if($file->size){
                        $response->header("Content-Length", $file->size);
                    }
                }
            } else if ($disk == 's3') {
                $bucket = Config::get('filesystems.disks.s3.bucket');
                $redirectToFile = "https://s3.amazonaws.com/$bucket/$file->path/$file->name";
            } else {
              return response()->json([
                  'success'   => false,
                  'error'     => 'File not found'
              ],404);
            }

            if ($file->fileable_type == 'App\\Song') {
              try {
                $user = JWTAuth::parseToken()->authenticate();
                $song = Song::find($file->fileable_id);

                //Create activity if user is logged in
                $activity = new Activity();
                $activity->fill([
                    'action'    => 'play',
                    'user_id'   => $user->id,
                    'reference_type'    => 'App\\Song',
                    'reference_id'      => $song->id
                ]);
                $activity->save();

                // Increment total plays
                $song->total_plays = $song->total_plays + 1;
                $song->save();

              } catch(JWTException $error) {
                // do nothing if user is not logged in
              }
            }

            // Redirect to amazon s3?
            if ($redirectToFile) {
                return redirect($redirectToFile);
            }

            return $response;
        }else{
            return response()->json([
                'success'   => false,
                'error'     => 'File not found'
            ],404);
        }
    }

    /**
     * Receives a song id, creates a user activity and increments the number
     * of plays on the given song.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function countPlays(Request $request, $id) {
      $song = Song::find($id);

      if ($song) {
          try {
            $user = JWTAuth::parseToken()->authenticate();

            //Create activity if user is logged in
            $activity = new Activity();
            $activity->fill([
                'action'    => 'play',
                'user_id'   => $user->id,
                'reference_type'    => 'App\\Song',
                'reference_id'      => $song->id
            ]);
            $activity->created_at = $request->time;
            $activity->save();

            // Increment total plays
            $song->total_plays = $song->total_plays + 1;
            $song->save();

            return response()->json([
                'success'   => true,
                'song'      => $song
            ]);
          } catch(JWTException $error) {
            // do nothing if user is not logged in
          }
          return response()->json([
              'success'   => false,
              'error'     => 'User is not logged in, therefore plays does not count'
          ]);
      } else {
          return response()->json([
              'success'   => false,
              'error'     => 'Song not found'
          ],404);
      }
    }

    /**
     * Only logged in users can download songs
     */
    public function download(Request $request, $id) {
        $config = Configuration::where('key', 'APP_DOWNLOAD_SONG_FILE')->first();
        if ($config != null && $config->value == '1') {
            $user = JWTAuth::parseToken()->authenticate();
            $song = Song::find($id);

            // @TODO: check if file exist in disk and send 404 if not
            // return response()->json([
            //     'success'   => false,
            //     'error'     => 'File not found'
            // ], 404);

            //Create activity if user is logged in
            $activity = new Activity();
            $activity->fill([
                'action'    => 'download',
                'user_id'   => $user->id,
                'reference_type'    => Song::class,
                'reference_id'      => $song->id
            ]);
            $activity->save();

            // Increment total plays
            $song->total_downloads = $song->total_downloads + 1;
            $song->save();

            $disk = Config::get('filesystems.default');

            if ($disk == 'local') {
                $path = storage_path() . '/app/public/' . $song->file->path . '/' . $song->file->name;
            } else if ($disk == 's3') {
                $bucket = Config::get('filesystems.disks.s3.bucket');
                $path = Storage::disk('s3')->url("$bucket/$song->file->path/$song->file->name");
            }
            
            return response()->download($path, $song->file->original_name, [
                "Content-Type" => $song->file->content_type,
            ]);
        }

        return response()->json([
            'success'   => false,
            'errors'    => [
                'Download songs is not enabled by the admin.'
            ]
        ], 400);
    }
}

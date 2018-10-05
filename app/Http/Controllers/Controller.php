<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Config;
use URL;
use Storage;

/**
 * Class Controller
 *
 * @package App\Http\Controllers
 *
 * @SWG\Swagger(
 *     basePath="",
 *     host="localhost:8000",
 *     schemes={"http"},
 *     @SWG\Info(
 *         version="1.0",
 *         title="Music App - API documentation",
 *         @SWG\Contact(name="Crysfel Villa", url="https://www.crysfel.com"),
 *     ),
 *
 *     @SWG\Definition(
 *         definition="Playlist",
 *         required={"title"},
 *         @SWG\Property(property="title", type="string"),
 *         @SWG\Property(property="public",type="boolean"),
 *     ),
 *     @SWG\Definition(
 *         definition="Error",
 *         required={"success", "message"},
 *         @SWG\Property(property="success",type="boolean"),
 *         @SWG\Property(property="message",type="string"),
 *     ),
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     *  Return the public URL for images from S3,
     *  all files are public!
     * @TODO When private files are supported, we need
     * to return private URLs that use the FileController for S3 storage
     */
    public function getFileURL($fileRecord){
      $disk = Config::get('filesystems.default');

      if ($disk == 's3') {
          $bucket = Config::get('filesystems.disks.s3.bucket');

          // If is a public file serve it directly from amazon s3
          if (isset($fileRecord->public)) {
            return $fileRecord->public
              ? "https://s3.amazonaws.com/$bucket/$fileRecord->path/$fileRecord->name"
              : URL::to('/').'/api/v1/files/'.$fileRecord->file_id;;
          }

          return "https://s3.amazonaws.com/$bucket/$fileRecord->path/$fileRecord->name";
      } else {
        if (isset($fileRecord->public)) {
          return $fileRecord->public
            ? URL::to('/')."/storage/$fileRecord->path/$fileRecord->name"
            : URL::to('/').'/api/v1/files/'.$fileRecord->file_id;
        }
          return URL::to('/').'/api/v1/files/'.$fileRecord->file_id;
      }
    }
}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Repositories\AlbumRepository;
use App\Repositories\ActivityRepository;
use App\Repositories\UserRepository;
use App\Http\Requests;
use App\Http\Serializers\UserSerializer;
use App\Http\Serializers\ActivitySerializer;
use App\Http\Controllers\Controller;
use JWTAuth;
use Validator;
use Config;
use Storage;
use URL;
use Gate;
use App\Album;
use App\User;
use App\Song;
use App\Post;
use App\Activity;
use App\File as FileEntry;
use App\Policies\UserPolicy;
use Carbon\Carbon;
use Log;

class UserController extends Controller
{
    /**
     * The album repository instance.
     *
     * @var ActivityRepository
     */
    protected $activityRepository;

    /**
     * The user repository instance.
     *
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        ActivityRepository $activityRepository,
        UserRepository $userRepository,
        AlbumRepository $albumRepository,
        UserSerializer $userSerializer,
        ActivitySerializer $activitySerializer
    )
    {
        $this->userSerializer = $userSerializer;
        $this->activitySerializer = $activitySerializer;

        $this->activityRepository = $activityRepository;
        $this->userRepository = $userRepository;
        $this->userRepository = $userRepository;
        $this->albumRepository = $albumRepository;
        $this->validations = [
            'name'         => 'required|min:3|max:255',
            'about'        => 'max:350',
            'image'        => 'max:10000|mimes:jpeg,png',
            'password'     => 'sometimes|required|between:7,50',
        ];
    }

    /**
     * Returns the current logged user
     *
     * @return \Illuminate\Http\Response
     */
    public function current()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $user->playlists;

        return response()->json([
            'success'   => true,
            'user'    => $this->userSerializer->one($user, ['basic', 'full', 'private']),
            'meta'    => []
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $users = User::filters([
            'author' => $request->input('authors') ? true : null,
            'name' => $request->input('search'),
            'gender' => $request->input('gender'),
            'country' => $request->input('country'),
        ])->orderBy('created_at', 'DESC')->paginate();

        return response()->json([
            'success'   => true,
            'meta'      => [
                'total'     => $users->total(),
            ],
            'users'     => $this->userSerializer->list($users->items(), ['basic']),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $current = JWTAuth::parseToken()->authenticate();
        $user = User::find($id);

        if($user){
            $data = ['basic', 'full'];

            if ($user->id == $current->id || $current->admin) {
                array_push($data, 'private');
            }

            return response()->json([
                'success'   => true,
                'user'    => $this->userSerializer->one($user, $data),
            ]);
        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ['User not found'],
            ],404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $current    = JWTAuth::parseToken()->authenticate();
        $user       = User::find($id);
        $image      = null;
        $updateAvatar = false;

        if (Gate::forUser($current)->allows('update-user', $user)) {

            $validator = Validator::make($request->all(), $this->validations);

            if ($validator->fails()) {
                return response()->json([
                    'success'=> false,
                    'errors' => $validator->errors()->all()
                ],400);
            }

            if($request->hasFile('image')){
                $image = $this->uploadImage($user, $request->image);
                $updateAvatar = true;
            }

            //These fields only admins can update
            if($current->admin){
                //validate admin only fields
                if($request->admin == 'true' || $request->admin == 'false'){
                    $user->admin = $request->admin == 'true';
                }
                if($request->author == 'true' || $request->author == 'false'){
                    $user->author= $request->author == 'true';
                }

                if($request->latitude){
                    $user->latitude= $request->latitude;
                }
                if($request->longitude){
                    $user->longitude= $request->longitude;
                }
                if($request->postcode){
                    $user->postcode= $request->postcode;
                }
            }

            $user->fill($request->all());

            // Update email if different than previous email
            if($request->input('email') && $request->input('email') != $user->email) {
                $validator = Validator::make($request->all(),[
                    'email' => 'required|email|max:255|unique:users',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success'=> false,
                        'errors' => $validator->errors()->all()
                    ],400);
                }

                $user->email = $request->email;
            }
            
            // Update image if is pressent
            if($image){
                $user->image = $this->getFileURL((Object)[
                  'file_id' => $image->id,
                  'name'    => $image->name,
                  'path'    => $image->path,
                  'public'    => $image->public,
                ]);
            }

            // Encrypt passwd if pressent
            if ($request->input('password')) {
                $user->password = bcrypt($request->input('password'));
            }

            $user->save();

            $activity = new Activity();
            if($updateAvatar){
                //create activitiy when changing avatar
                $activity->fill([
                    'action'            => 'avatar',
                    'user_id'           => $user->id,
                    'reference_type'    => 'App\\File',
                    'reference_id'      => $image->id
                ]);
                $activity->save();
            }else if(!$request->password){
                //create activitiy when updatting general information
                $activity->fill([
                    'action'            => 'user-info',
                    'user_id'           => $user->id,
                    'reference_type'    => 'App\\User',
                    'reference_id'      => $user->id
                ]);
                $activity->save();
            }

            return response()->json([
                'success'   => true,
                'message'   => 'User successfully updated.',
                'user'      => $this->prepareData($user,null)
            ]);
        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ["You don't have permissions to update this user."]
            ],403);
        }
    }

    /**
     * Display the public user activities feed
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function feed(Request $request, $id)
    {
        $current = JWTAuth::parseToken()->authenticate();
        $user = User::find($id);

        if($user){
            if ($current->id == $user->id && $user->followings()->count() > 0) {
                $activities = $this->activityRepository->getFeedForUser($user, $request->input('page'));
            } else {
                $activities = $this->activityRepository->getLatestsByUser($user, $request->input('page'));
            }

            return response()->json([
                'success'   => true,
                'meta'      => [
                ],
                'activities'=> $this->activitySerializer->list($activities, ['basic']),
            ]);
        }

        return response()->json([
            'success'   => false,
            'errors'    => ['User not found'],
        ], 404);
    }

    /**
     * Returns a list of unique countries for all users
     */
    public function countries(Request $request) {
        $countries = User::countries($request->input('authors'))->get();

        return response()->json([
            'success'   => true,
            'meta'      => [
            ],
            'countries'=> $countries,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $current = JWTAuth::parseToken()->authenticate();
        $user       = User::find($id);

        if (!$user) {
            return response()->json([
                'success'   => false,
                'errors'    => ['User not found'],
            ], 404);
        }

        if (Gate::forUser($current)->allows('delete-user', $user)) {
            try {
                $user->delete();
                return response()->json([
                    'success'   => true,
                    'message'   => 'User successfully removed',
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'success'   => false,
                    'errors'    => [$e->getMessage()],
                ], 500);
            }
        }

        return response()->json([
            'success'   => false,
            'errors'    => ['You don\'t have access to this resource'],
        ], 403);
    }

    private function uploadImage($user, $file){
        $path = Config::get('paths.user.avatar');
        $path = str_replace('{user_id}', $user->id, $path);
        $name = $file->getFilename().'.'.$file->getClientOriginalExtension();

        Storage::put($path.'/'.$name,  file_get_contents($file), 'public');

        $image = new FileEntry();
        $image->fill([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
            'path'          => $path,
            'content_type'  => $file->getClientMimeType(),
            'size'          => filesize($file)
        ]);
        $image->fileable_type = 'App\\User';
        $image->fileable_id   = $user->id;
        $image->public        = true;
        $image->save();

        return $image;
    }

    private function prepareData($record, $activities){
        $current = JWTAuth::parseToken()->authenticate();
        $user = [
            'id'        => $record->id,
            'name'      => $record->name,
            'country'   => $record->country,
            'website'   => $record->website,
            'about'     => $record->about,
            'gender'    => $record->gender,
            'dob'       => $this->fixDateFormat($record->dob),
            'image'     => $record->image,
            'occupation'=> $record->occupation,
            'author'    => $record->author,
            'admin'     => $record->admin,
        ];

        if($current->admin){
            $user['latitude'] = $record->latitude;
            $user['longitude'] = $record->longitude;
            $user['postcode'] = $record->postcode;
            $user['email'] = $record->email;
        }

        if($current->id == $record->id){
            $user['email'] = $record->email;
        }

        if($activities){
            $user['activities'] = $this->prepareActivitiesData($activities);
        }

        return $user;
    }

    private function prepareActivitiesData($activities){
      $latest = [];
      foreach ($activities as $act) {
          $append = true;
          $activity = [
              'id'            => $act->id,
              'action'        => $act->action,
              'time'          => $act->created_at,
          ];

          switch ($act->action) {
              case 'published-album':
                    $album = $this->albumRepository->find($act->reference_id);
                    if ($album) {
                        $activity['album'] = [
                        'id'     => $album->id,
                        'title'  => $album->title,
                        'image'  => $this->getFileURL((object)[
                            'file_id' => $album->file_id,
                            'name'  => $album->file_name,
                            'path'  => $album->path,
                            'public'  => $album->public,
                        ]),
                        ];
                    } else {
                        $append = false;
                    }
                  break;
              case 'avatar':
                  $activity['image'] = $this->getFileURL((object)[
                    'file_id' => $act->file_id,
                    'name'  => $act->name,
                    'path'  => $act->path,
                    'public'  => $act->public,
                  ]);
                  break;
              case 'follow':
                $user = User::find($act->reference_id);
                if (isset($user)) {
                    $activity['user'] = [
                        'id'  => $user->id,
                        'name'  => $user->name,
                        'image' => $user->image,
                    ];
                }
              case 'like' || 'unlike' || 'play' || 'comment':
                  if ($act->reference_type == 'App\\Song') {
                    $song = Song::find($act->reference_id);
                    if ($song) {
                        $activity['type'] = 'song';
                        $activity['song'] = [
                            'id'  => $song->id,
                            'title'  => $song->title,
                        ];
                    } else {
                        $append = false;
                    }
                  }

                  if ($act->reference_type == 'App\\Post') {
                    $post = Post::find($act->reference_id);

                    if ($post) {
                        $activity['type'] = 'post';
                        $activity['post'] = [
                            'id'  => $post->id,
                            'title'  => $post->title,
                        ];
                    } else {
                        $append = false;
                    }
                  }
                  break;
          }

          if ($append) {
            array_push($latest, $activity);
          }
      }
      return $latest;
    }

    // Quick and dirty way to fix date formats coming from database
    // Old version was saving data as YYYY-MM-DD, newer clients are saving it YYYY-MM-DD hh:mm:ss
    // this function generates the right format for all dates.
    private function fixDateFormat($date) {
        if ($date) {
            $result =  new Carbon($date);

            return $result->toDateTimeString();
        }

        return '';
    }
}

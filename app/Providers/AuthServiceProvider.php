<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if ($user->admin) {
                return true;
            }
        });

        Gate::define('update-user', function ($current, $user) {
            return $user->id === $current->id;
        });
        Gate::define('delete-user', function ($current, $user) {
            return $user->id === $current->id;
        });


        Gate::define('albums', function ($user, $album) {
            return $user->author;
        });
        Gate::define('albums-create', function ($user, $album) {
            return $user->author;
        });
        Gate::define('albums-show', function ($user, $album) {
            return $user->id == $album->user_id;
        });
        Gate::define('albums-update', function ($user, $album) {
            return $user->id == $album->user_id;
        });
        Gate::define('albums-delete', function ($user, $album) {
            return $user->id == $album->user_id;
        });


        Gate::define('genres', function ($user, $genre) {
            return $user->author;
        });
        Gate::define('genres-create', function ($user, $genre) {
            return $user->author;
        });
        Gate::define('genres-update', function ($user, $genre) {
            return $user->admin;
        });
        Gate::define('genres-delete', function ($user, $genre) {
            return $user->admin;
        });


        Gate::define('comments', function ($user, $comment) {
            return $user->admin;
        });
        Gate::define('comments-show', function ($user, $comment) {
            return $user->id == $comment->user_id;
        });
        Gate::define('comments-delete', function ($user, $comment) {
            return $user->id == $comment->user_id;
        });
        Gate::define('comments-update', function ($user, $comment) {
            return $user->id == $comment->user_id;
        });
        Gate::define('comments-create', function ($user, $comment) {
            return true; //Anyone can comment
        });

        Gate::define('blog', function ($user, $post) {
            return $user->author;
        });
        Gate::define('blog-create', function ($user, $post) {
            return $user->author;
        });
        Gate::define('blog-update', function ($user, $post) {
            return $user->id == $post->author_id;
        });
        Gate::define('blog-show', function ($user, $post) {
            return $user->id == $post->author_id;
        });
        Gate::define('blog-delete', function ($user, $post) {
            return $user->id == $post->author_id;
        });

        Gate::define('playlist-edit', function ($user, $playlist) {
            return $user->id == $playlist->user_id;
        });
        Gate::define('playlist-show', function ($user, $playlist) {
            return $user->id == $playlist->user_id;
        });
        Gate::define('playlist-delete', function ($user, $playlist) {
            return $user->id == $playlist->user_id;
        });
        Gate::define('playlist-add-song', function ($user, $playlist) {
            return $user->id == $playlist->user_id;
        });
        Gate::define('playlist-remove-song', function ($user, $playlist) {
            return $user->id == $playlist->user_id;
        });
        
        Gate::define('config-create', function ($user, $config) {
            return $user->admin ? true : false;
        });
        Gate::define('config-update', function ($user, $config) {
            return $user->admin ? true : false;
        });
        Gate::define('config-index', function ($user, $config) {
            return $user->admin ? true : false;
        });
        Gate::define('config-styles', function ($user, $config) {
            return $user->admin ? true : false;
        });
        
        Gate::define('statistics', function ($user, $config) {
            return $user->author? true : false;
        });
    }
}

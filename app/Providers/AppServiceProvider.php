<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\File as FileEntry;
use Storage;
use Ramsey\Uuid\Uuid;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Supporting old versions of mysql
        Schema::defaultStringLength(191);

        //remove the file from disk when the model
        //is removed from the database
        FileEntry::deleting(function ($file) {
            if (Storage::exists($file->path.'/'.$file->name)) {
                Storage::delete($file->path.'/'.$file->name);
            }
        });

        //Adding the UUID to the file table
        FileEntry::creating(function ($file) {
            $file->id = Uuid::uuid1();
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

## My Music App
This software allows you to create your own community about music, the idea is to allow other users to upload their own music, other people can listen, vote, comment and interact with the authors and their music.

##Software Stack
This software is based on the awesome Laravel 5.1 framework, it uses a few other packages to manage the authentication using tokens with JWT, generate UUIDs, geo ip, etc. You can take a look to the composer.json file to see all the dependencies. The backend is only a JSON Rest API, all communication is based on JSON, this allow us to use any client in the frontend, excellent to developer an Webapp or even a mobile app using the same backend.

The frontend is built on Angular JS, all the code is under the frontend folder, it [uses webpack](https://webpack.github.io/) as a build system and npm to manage dependecies. The frontend is complete different project, it can run on a different server and request the data from the Laravel server, check the install instructions.

## Installation
The first thing to install is the backend server, the installation is just like any other laravel project, install the dependencies, set configurations, run migrations, seed the database (optional for development only) and run the server.

###Increasing max file size
In order to upload mp3 files, it's required to increase the max file size, open the `php.ini` file and set the following values:

    upload_max_filesize = 50M
    post_max_size = 50M

In Mac OS:
- If you used **homebrew** to install the latest version of php, you need to update `/usr/local/etc/php/7.2/php.ini` (Make sure to update the right version of php).
- If you are using the default php installation you can find this file in `/etc/php.ini` (In high sierra, this file doesn't exist, you need to create it by using `/etc/php.ini.default` as a template).

###Environment variables
Laravel uses several environment variables, these variables are defined inside of the *.env* file, you need to create this file by opening the *.env.example*. You need to copy the variables from the example and modify the value according to your needs.

Here's an example of how I did it in my development environment:

    APP_ENV=local
    APP_DEBUG=true
    APP_KEY=tXS3AdFagNq8lRq02wzyaZ6uN014r2SB

    DB_HOST=127.0.0.1
    DB_DATABASE=music
    DB_DATABASE_TEST=music_test
    DB_USERNAME=root
    DB_PASSWORD=

    CACHE_DRIVER=file
    SESSION_DRIVER=file
    QUEUE_DRIVER=sync

    MAIL_DRIVER=smtp
    MAIL_HOST=mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=null
    MAIL_PASSWORD=null
    MAIL_ENCRYPTION=null

    JWT_SECRET=BFfZN9gwGBzJRC5Gcwks2zZNZOqICtaA
    JWT_FACEBOOK_SECRET=123456789abcdef123456
    JWT_TWITTER_KEY=null
    JWT_TWITTER_SECRET=null

    S3_KEY=123456789abcdef123456
    S3_SECRET=123456789abcdef123456
    S3_BUCKET=test
    S3_REGION=us-east-1

Make sure you enter the correct values for your environment.

To generate a random APP_KEY, you can run the following command.

    php artisan key:generate

### Installing dependencies
This is a very simple step, just run the following command on your terminal, remember to execute the command on the root of the project.

    $ composer install

If you are deploying your project to production, then you don't need the development requirements, in order to ignore those packages run the following command instead of the previous one. Also you should optimize the autoloader in production.

    $ composer install --no-dev --optimize-autoloader

That's it! All dependencies should be installed by now.


### Installing laravel-geoip
[laravel-geoip](https://github.com/Torann/laravel-geoip) is used to get the location of new users based on their IP, to do this Geo IP uses a database that needs to be [downloaded from the internet](http://dev.maxmind.com/geoip/geoip2/geolite2/), this project already have an artisan task to download the database automatically for you.

    $ php artisan geoip:update

And that's it! If you don't want to use the artisan task, you can always download the database manually, then just unzip it under the *storage/locations* folder.

If you want to change the location of the database, just open the *config/geoip.php* file and set the new location in there. You can also set the default values when the IP is not found in the database.

### Configure the database
By default this software uses MySql, make sure you have it installed and running in your system. Also you will need to create a new Database for this project, a user and password with read and write access to the new database. Make sure the credentails are defined in the *.env* file.

Once you have defined your credentials in the *.env* file you will need to run the migrations, this will create all the tables in the database.

    $ php artisan migrate


##Seeding data for development
In order to setup your development environment, is recommended to seed your database with dummy data, by running the following command on the root of the project several records will be created in the development database.

    $ php artisan db:seed

**IMPORTANT: Do not run this command on production!** The seed will clear the database first and then it will insert the new data. Everything will be removed, make sure you don't run this comman on production environment.

Fake users will be created, albums with songs will be added to a few users. All assets are free to use under CC.

User images: https://www.flickr.com/photos/gregpc/
Album images: https://unsplash.it/
Music: http://www.bensound.com

##Seeding data for production
Make sure to run the following command only once in your production environment.

    $ php artisan db:seed --class=ProductionSeeder

This script will only create an admin user, it doesn't remove any data. It only adds a new record to the *users* table and to the *activities* table.

Once you run this script you will be able to login into the app using the following credentials:

user: admin@admin.com
pass: admin123

**IMPORTANT: Make sure to change the email/password from the users module on the administration section.**

## Configure Facebook
You can use facebook to login into the app, first you need to register your app in the facebook developer platform. Then you need to set the App ID and the Private Key.

## API documentation
This project use swagger to document the API, after the project is running correctly you can open http://localhost:8000/swagger/index.html in your browser to see all the available endpoints, you can tests every endpoint from this same page.

You might not want to upload documentation to your production environment, make sure to remove `public/docs` and `public/swagger` folders when deploying to production.

## Preparing release
Checklist to prepare bundle.

1. Download latest from master
2. Build client locally but remember to update the `.env` file to set the `SERVER_API_URL=/api`
3. Copy build to final bundle
4. Copy `vendor` folder to final bundle
5. Remove script automated deployment script from bundle
6. Update documentation to new version
7. Create zip file with build and documentation

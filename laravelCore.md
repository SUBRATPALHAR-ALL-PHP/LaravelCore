# Laravel Core Learning

DOCS
    https://laravel.com/docs/7.x
ApiDocumentation
    https://laravel.com/api/7.x
Release Info
    [Bug fixes & New features]
    https://laravel.com/docs/7.x/releases

    https://laravel.com/docs/7.x/upgrade
    https://laravel.com/docs/7.x/contributions



Installation:
    see all installation guide @ https://laravel.com/docs/7.x

    composer global require laravel/installer
    laravel new blog

    Via Composer Create-Project
    composer create-project --prefer-dist laravel/laravel ProjectName

    you may use Homestead Or Vallet - for development server.


    - Create a .env file
    - run to setup key in .env file => php artisan key:generate
        If the application key is not set, your user sessions and other encrypted data will not be secure!

    APP_NAME=Laravel, APP_ENV=local, APP_KEY, APP_DEBUG, APP_URL=http://localhost

Configuration:
    \/ Public Directory
        web server's document / web root
        The index.php is front controller for all HTTP requests entering into application.
    \/ Configuration Files
        All of the configuration files for the Laravel framework are stored in the config directory.
    \/ Directory Permissions
        Directories within the storage and the bootstrap/cache directories should be writable by your web server or Laravel will not run.
    \/ Additional Configuration
        config/app.php file contains several options such as timezone and locale that you may wish to change according to your application.
    \/ You may also want to configure a few additional components of Laravel, such as:
        Cache
        Database
        Session

Web Server Configuration:
    - Directory Configuration
        Laravel should always be served out of the root of the `"web-directory" configured for your "web-server"`.
        You should not attempt to serve a Laravel application `out of a subdirectory of the "web-directory"`.
        Attempting to do so could expose sensitive files present within your application.

    - Pretty URLs
        Laravel includes a public/.htaccess file that is used to provide URLs without the index.php front controller in the path.
        Before serving Laravel with Apache, be sure to enable the mod_rewrite module so the .htaccess file will be honored by the server.

        If you are using Nginx, the following directive in your site configuration will direct all requests to the index.php front controller:
        location / { try_files $uri $uri/ /index.php?$query_string; }



Add virtual mc setup @ later


=======================================================================
Chapter 1 => Prologue
=======================================================================


=======================================================================
Chapter 2 => Getting Started
=======================================================================

--------------------------
Installation
--------------------------


--------------------------
Configuration
--------------------------


--------------------------

--------------------------



--------------------------

--------------------------



--------------------------

--------------------------


=======================================================================
Chapter 3 => Architecture Concepts
=======================================================================




=======================================================================
Chapter 4 => The Basics
=======================================================================



=======================================================================
Chapter 5 => Frontend
=======================================================================




=======================================================================
Chapter 6 => Security
=======================================================================





=======================================================================
Chapter 7 => Digging Deeper
=======================================================================





=======================================================================
Chapter 8 => Database
=======================================================================





=======================================================================
Chapter 9 => Eloquent ORM
=======================================================================




=======================================================================
Chapter 10 => Testing
=======================================================================



=======================================================================
Chapter 11 => Official Packages
=======================================================================







=======================================================================
LATER
=======================================================================
Investigate what this does ?
    > @php -r "file_exists('.env') || copy('.env.example', '.env');"


php artisan migrate:fresh
  will create 2 tables `users` & `failed_jobs`
php artisan db:seed




--------------------------------------------------------------------------
FORM BASED BASIC AUTHENTICATION
--------------------------------------------------------------------------
  [https://laravel.com/docs/7.x/authentication]

  composer require laravel/ui

  php artisan ui vue --auth
  npm install && npm run dev


-------------------------------------------------------------------------
SOCIALITE
-------------------------------------------------------------------------

composer require laravel/socialite
https://developers.google.com/identity/sign-in/web/sign-in


callingFrom: webserver
callbackURL: http://localhost:8000/login/google/callback


add these values to .env
  clientID=231779600494-v12vniuife8lq23jtpvvkqpsbvp6huqh.apps.googleusercontent.com
  clientSecret=01weuhUO9YiayLWx2DSNC1hk

in configServices.php
  'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT'),
  ],

in app/Http/Controllers/Auth/LoginController.php
  use Socialite;
  public function redirectToProvider() {
    return Socialite::driver('google')->redirect();
  }
  public function handleProviderCallback() {
    $user = Socialite::driver('google')->user();
    print_r($user);
  }

Router configuration
  Route::get('login/google', 'Auth\LoginController@redirectToProvider');
  Route::get('login/google/callback', 'Auth\LoginController@handleProviderCallback');














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



-------------------------------------------------------------------------
AUTHORIZATION
-------------------------------------------------------------------------
there are two primary ways of authorizing actions:
  - gates (routes)
    > Closure based approach to authorization.
    > Gates are most applicable to `actions which are not related to any model or resource`
  - policies (controllers)
    > Like controllers, `group their logic` around a `particular model or resource`.
    > policies should be used when you wish to
      `authorize an (action) for a particular {model} or {resource}`.
_____
Gates
  `Gates` are ```Closures``` that determine if a user is authorized to perform a given action
  and are `typically defined in the App\Providers\AuthServiceProvider class` `using the Gate facade`.

  Gates always receive a `user instance` as their `first argument`,
  and may `optionally` receive `additional arguments` such as a `relevant Eloquent model`

  /** Here we are defining, actions such as : 'edit-settings', 'update-post' */
  public function boot() {
    $this->registerPolicies();
    Gate::define('edit-settings', function ($user) {return $user->isAdmin;}); //$user
    Gate::define('update-post', function ($user, $post) {return $user->id === $post->user_id;}); //$user, model $post
    Gate::define('update-post', 'App\Policies\PostPolicy@update'); //class{}@methodName()
  }

  After you define the actions, you will authorise a user to an action,, 'edit-settings', 'update-post'
    Gate::allows('edit-settings'))
    Gate::denies('update-post', $post)

    Gate::forUser($user)->allows('update-post', $post) // for a particular user

    Gate::any(['update-post', 'delete-post'], $post) // all action
    Gate::none(['update-post', 'delete-post'], $post)

    /**
     * Attempt to authorize an action
     * and automatically throw an Illuminate\Auth\Access\AuthorizationException
     * if the user is not allowed to perform the given action
     */
    Gate::authorize('update-post', $post);


  Supplying Additional Context
    The gate methods for authorizing abilities (allows, denies, check, any, none, authorize, can, cannot)
    and the authorization Blade directives (@can, @cannot, @canany)
    can receive an array as the second argument.
    These array elements are passed as parameters to gate,
    and can be used for additional context when making authorization decisions:

    if (Gate::check('create-post', [$category, $extraFlag])) {}
    ||
    \/
    Gate::define('create-post',function($user,$category,$extraFlag){return $category->group>3&&$extraFlag===true;});

  Gate Responses
    $response = Gate::inspect('edit-settings', $post);
      if ($response->allowed()) {} else {echo $response->message();}

    When returning an authorization response from your gate,
    the Gate::allows method will still return a simple boolean value;
    however, you may use the Gate::inspect method to get the full authorization response returned by the gate.

  Intercepting Gate Checks
    Gate::before(function ($user, $ability) {
      if ($user->isSuperAdmin()) {return true;}
    });
    If the before callback returns a non-null result that result will be considered the result of the check.

    Gate::after(function ($user, $ability, $result, $arguments) {
      if ($user->isSuperAdmin()) {return true;}
    });
    if the after callback returns a non-null result that result will be considered the result of the check.


________
Policies
  Policies are classes that organize authorization logic around a particular model or resource.

  php artisan make:policy PostPolicy
  /** Post model */

  php artisan make:policy PostPolicy --model=Post
  /** generate a class with the basic "CRUD" policy methods */

  All policies are resolved via the Laravel service container,
  allowing you to type-hint any needed dependencies [in the policy's constructor] to have them automatically injected.

  Register a policy
    Registering a policy will instruct Laravel,
    which policy to utilize when `authorizing 'actions' against a given 'model'`.

  Registering a policy in the auth service provider,,
    protected $policies = [Post::class => PostPolicy::class,];
    public function boot(){$this->registerPolicies();}
    -- Bcz,, you have registered policy,, which have create, update methods,,
      when a user will make a request, for creating a data,,
      then, policy's create method will execute,, where you put authorisation logic
      & all authorization check will be done there.

  index	viewAny
  show	view
  create	create
  store	create
  edit	update
  update	update
  destroy	delete
  forceDelete


  public function update(User $user, Post $post){
    return $user->id === $post->user_id;
  }
  public function update(User $user, Post $post){
    return $user->id === $post->user_id? Response::allow(): Response::deny('You do not own this post.');
  }

  If the incoming HTTP request was not initiated by an authenticated user,
  all gates and policies automatically return false.


  - Policy Filters
  For `certain users`, you may wish to `authorize all actions` `within a given policy`.
  To accomplish this, `define a before method` on the policy.
  The `before method will be executed before any other methods on the policy`,
  giving you an `opportunity to authorize the action` `before the intended policy method is actually called`.
  This feature is most commonly used for authorizing application administrators to perform any action:
    public function before($user, $ability) {
      `if ($user->isSuperAdmin()) {return true;}`
    }
  If you would like to `deny all authorizations` for a user you should `return false` from the before method.
  If `null` is returned,(in the before() method) the `authorization will fall through to the policy method`.

  The `before method` of a policy class `will not be called`
  if the `class doesn't contain a method` `with a name` `matching the name of the ability` `being checked`.




======================================================
  WorkingPlan
======================================================
laravel has pre-defined method mapping, to a certain action, as below..
  _______________________________
  ControllerMethod	PolicyMethod
    index             viewAny
    show              view
    create            create
    store             create
    edit              update
    update            update
    destroy           delete

  & then you can take the method name as,
    Gate::define('edit-settings', function ($user) {
      return $user->isAdmin;
    });

  Authorisation Fundamental & database design
    USER: Any user in the app.
    ROLE: Predefined role for an app, (ex-admin, manager, general-user)
    PERMISSION: Access to `model` | `Resources` | `Route` for an `user`|`role`
      *a USER have only one ROLE @ a given point of time

  _______________________________
  USER
  -------------------------------
  id  | role_id
  -------------------------------
  ________________________________
  ROLE
  --------------------------------
  id  | name  | description
  --------------------------------
  _________________________________________
  PERMISSION
  -----------------------------------------
  id  | name  | key | controller  | method
  -----------------------------------------
  ex: create, update, delete
  ----------------------------------------
  _________________________________________
  PERMISSION-ROLE [Mapper]
  -----------------------------------------
  permission_id | role_id
  -----------------------------------------
  -----------------------------------------

  user<--->role [one-to-one]
  role<--permission_role-->permission [many-to-many]
    (So-we-need-a-mapper-table : PERMISSION<>ROLE)

    we insert new permissions in the permissions table. After all, we attach all permissions to the admin role.
    /** This could be done one time, from the seed */
    $permission_ids = []; // an empty array of stored permission IDs
    // iterate though all routes
    foreach (Route::getRoutes()->getRoutes() as $key => $route) {
      $action = $route->getActionname(); // get route action
      $_action = explode(‘@’,$action); // separating controller and method
      $controller = $_action[0];
      $method = end($_action);
      // check if this permission is already exists
      $permission_check = Permission::where([‘controller’=>$controller,’method’=>$method])->first();
      if(!$permission_check){
        $permission = new Permission;
        $permission->controller = $controller;
        $permission->method = $method;
        $permission->save();
        // add stored permission id in array
        $permission_ids[] = $permission->id;
      }
    }
    $admin_role = Role::where(‘name’,’admin’)->first(); // find admin role.
    $admin_role->permissions()->attach($permission_ids); // atache all permissions to admin role

    /***********/
    // get user role permissions
    $role = Role::findOrFail(auth()->user()->role_id);
    $permissions = $role->permissions;
    $actionName = class_basename($request->route()->getActionname()); // get requested action
    foreach ($permissions as $permission) { // check if requested action is in permissions list
      $_namespaces_chunks = explode(‘\\’, $permission->controller);
      $controller = end($_namespaces_chunks);
      if ($actionName == $controller . ‘@’ . $permission->method) { // authorized request
        return $next($request);
      }
    }
    return response(‘Unauthorized Action’, 403); // none authorized request



======================================================
  Working plan as per Laravel Docs
======================================================
  There are two primary ways of authorizing actions: gates & policies

  inside your project App\Providers\AuthServiceProvider using Gate facade,
  determine the authorisation state for an user.




  index	viewAny
  show	view
  create	create
  store	create
  edit	update
  update	update
  destroy	delete
  forceDelete



  public function boot() {
    $this->registerPolicies();

    /** returns a boolean value */

    Gate::define('edit-settings', function ($user) {return $user->isAdmin;});
    Gate::define('update-post', function ($user, $post) {return $user->id === $post->user_id;});
    Gate::define('update-post', 'App\Policies\PostPolicy@update');

    if (Gate::allows('edit-settings')) { // The current user can edit settings }
    if (Gate::denies('update-post', $post)) { // The current user can't update the post... }
    if (Gate::forUser($user)->allows('update-post', $post)) {// The user can update the post...}

    if (Gate::any(['update-post', 'delete-post'], $post)) {
      // The user can update or delete the post
    }
    if (Gate::none(['update-post', 'delete-post'], $post)) {
      // The user cannot update or delete the post
    }

    Gate::authorize('update-post', $post);


    Gate::define('edit-settings', function ($user) {
      return $user->isAdmin? Response::allow(): Response::deny('You must be a super administrator.');
    });

    Gate::authorize('edit-settings', $post);


    Gate::before(function ($user, $ability) {
      if ($user->isSuperAdmin()) {
        return true;
      }
    });



    /**
     * php artisan make:policy PostPolicy --model=Post
     *
     * Map Policy
     * protected $policies = [ Post::class => PostPolicy::class,];
     *
     * Specifically, the policies must be in a Policies directory below the directory that contains the models.
     *
     */
    class PostPolicy {
      /**
       * Determine if the given post can be updated by the user.
       *
       * @param  \App\User  $user
       * @param  \App\Post  $post
       * @return bool
       */
      public function update(User $user, Post $post) {
        return $user->id === $post->user_id;

        return $user->id === $post->user_id
          ? Response::allow()
          : Response::deny('You do not own this post.');
      }
    }

}

Authorizing Actions Using Policies
  -- Via The User Model
    The User model that is included with your Laravel application includes two helpful methods for authorizing actions:
    can and cant.

    let's determine if a user is authorized to update a given Post model:
    if ($user->can('update', $post)) {//}
    If a policy is registered for the given model, [Post]
    the can method will automatically call the appropriate policy (postPolicy-update())
    and return the boolean result. 

  Via Middleware
    By default,
    the Illuminate\Auth\Middleware\Authorize middleware is assigned the can key in your App\Http\Kernel class.
    Route::put('/post/{post}', function (Post $post) {
      // The current user may update the post...
    })->middleware('can:update,post');

    The class name will be used to determine which policy to use when authorizing the action:
    Route::post('/post', function () {
      // The current user may create posts...
    })->middleware('can:create,App\Post');

  Via Controller Helpers
    public function update(Request $request, Post $post){
      $this->authorize('update', $post);
      // The current user can update the blog post...
    }

    public function create(Request $request){
      $this->authorize('create', Post::class);
      // The current user can create blog posts...
    }

  Authorizing Resource Controllers
    public function __construct(){
      $this->authorizeResource(Post::class, 'post');
    }



--------------------------------------------------------------------------
EMAIL & QUEUE
--------------------------------------------------------------------------
  With simple API over the popular [SwiftMailer-library]
  with [drivers] for SMTP, [Mailgun], Postmark, Amazon SES, and sendmail,


  We will trigger an email,
    Mail::to($request->user())->send(new SubratTest());

  STEPS::
    define a route to trigger email
    Route::get('/sendEmail', 'SendEmailController@index');
    localhost/sendEmail

    create a controller
      SendEmailController
        index() {
          $user = new \stdClass();
          $user->email='delta.palmate@gmail.com';
          $Test = Mail::to($user)->send(new SubratTest()); // SubratTest is a mailable
        }
    
    Generating Mailables
      In Laravel, each type of email sent by your application is represented as a "mailable" class.
      These classes are stored in the app/Mail directory.

      php artisan make:mail SubratTest // --markdown=emails.orders.shipped

      mailable class build() method, takes customise configuration,
        define a 'MailTemplate.blade.php' // resources/views/mail/mail.blade.php

        public function build() {
          return $this->view('mail.mail');
        }

        any public property defined on your mailable class will automatically be made available to the view.
          public function __construct(Order $order){$this->order = $order;}
        or you can pass data via with() method
          return $this->view('mail.mail')->with(['orderName' => $this->order->name,'orderPrice' => $this->order->price,]);


    [API-based-drivers] such as [Mailgun] and Postmark are often simpler and faster than SMTP servers.
    To use the [Mailgun driver],
    first install "Guzzle",
    then set the default option in your config/mail.php configuration file to mailgun.
  
    ``composer require guzzlehttp/guzzle
    


    Configure

      config/mail.php
        'default' => env('MAIL_MAILER', 'mailgun'), // driver setup
        'from' => ['address' => env('MAIL_FROM_ADDRESS'),'name' => env('MAIL_FROM_NAME')],

      config/services.php
        'mailgun' => [
          'domain' => env('MAILGUN_DOMAIN'),
          'secret' => env('MAILGUN_SECRET'),
          'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
          // baseurl will autosetup don't give full https address ../v3/domain..
        ],

    .env values
      navigate to mailgun dashboard->sending->overview->API|smtp
      get creds for api

      MAIL_MAILER=mailgun
      MAIL_HOST=smtp.mailgun.org
      MAIL_PORT=587
      MAIL_USERNAME=postmaster@sandbox0123456789.mailgun.org
      MAIL_PASSWORD=0123456789
      MAIL_ENCRYPTION=null
      MAIL_FROM_ADDRESS='email@email.com'
      MAIL_FROM_NAME="${APP_NAME}"

      MAILGUN_DOMAIN=sandbox1234567890.mailgun.org
      MAILGUN_SECRET=yoursecret-secret-secret



  -----------
  Queueing an email
    $Test = Mail::to($user)->send(new SubratTest());
    $Test = Mail::to($user)->queue(new SubratTest());
                           ->later($when, new SubratTest());
    
  Pushing To Specific Queues
    All mailable classes generated using make:mail command, use Illuminate\Bus\Queueable trait,
    you may call the `onQueue` and `onConnection` methods on any `mailable class instance`,
    to specify the `connection` and `queue-name`:

    $message = (new SubratTest())->onConnection('sqs')->onQueue('emails');

  Queueing By Default
    you want to always be queued,
    you may implement the `ShouldQueue contract` on the class.
    the `mailable` will `still be queued` since it `implements the contract`:


Queues
  A `Connection` have a `Queue` that `Dispatches` a `JOB`
  & `Worker` work on it(job)
  
  In practical A DRIVER(conection) wait for a JOB(REDIS)

  So, lets create a JOB
    ``php artisan make:job EmailJob
    The generated class will implement the Illuminate\Contracts\Queue\ShouldQueue interface,
    indicating to Laravel that the job should be pushed onto the queue to run `asynchronously`.

    dispatch a job,
      EmailJob::dispatch($podcast);
      EmailJob::dispatch($podcast)->delay(now()->addMinutes(10));
      dispatch(function () {
        Mail::to('taylor@laravel.com')->send(new WelcomeMessage);
      })->afterResponse();

    php artisan queue:work --tries=3
    Job classes handle() method is called when the job is processed by the queue.

  Connection
    connection to a backend service
    queue backends, such as Beanstalk, Amazon SQS, Redis, or even a relational database.
    any given connection can have multiple queue(stack of queue)

  Configuration[config/queue.php]
    default queue
      each connection configuration example in config/queue.php contains a queue attribute.

      `connection configurations` for `database`, `Beanstalkd`, `Amazon SQS`, `Redis`,
      and a `synchronous driver` -- that will execute jobs immediately (for local use).
    
    Connection Configuration for Database
      php artisan queue:table
      php artisan migrate
    Connection Configuration for Redis
      'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => '{default}',
        'retry_after' => 90,
      ],
    

  Connections Vs. Queues
    $message = (new SubratTest())->onConnection('sqs')->onQueue('emails');

  run a queue
  php artisan queue:work --queue=high,default

  -----
    1) Create a Job
      php artisan make:job EmailJob

      when you will request for to send email,,
      http://localhost:8000/sendEmail
      EmailJob::dispatch( Mail::to($user)->send(new SubratTest()));
      or with helper method
      dispatch(function () {Mail::to('delta.palmate@gmail.com')->send(new SubratTest());})->delay(now()->addMinutes(1));

    2) configure default queue to database
      .env QUEUE_CONNECTION=database
      /config/queue.php 'default' => env('QUEUE_CONNECTION', 'sync'),

    3) setup database
      php artisan queue:table
      php artisan queue:failed-table
      php artisan migrate

    4) php artisan queue:work --tries=3


    {
      "uuid":"d16e849b-72b4-465e-a644-6ba1b2cb17f9",
      "displayName":"App\\Jobs\\EmailJob","job":"Illuminate\\Queue\\CallQueuedHandler@call",
      "maxTries":null,
      "maxExceptions":null,
      "delay":null,
      "timeout":null,
      "timeoutAt":null,
      "data":{
        "commandName":"App\\Jobs\\EmailJob",
        "command":"O:17:\"App\\Jobs\\EmailJob\":8:{s:6:\"\u0000*\u0000job\";N;s:10:\"connection\";N;s:5:\"queue\";N;s:15:\"chainConnection\";N;s:10:\"chainQueue\";N;s:5:\"delay\";N;s:10:\"middleware\";a:0:{}s:7:\"chained\";a:0:{}}"
      }
    }







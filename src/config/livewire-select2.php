<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    |
    | You can cinfugre your theme, provider by select2.org
    |
    | can be: classic or bootstrap5
    */

    'theme' => 'bootstrap-5',

    /*
    |--------------------------------------------------------------------------
    | Optional prefix for routes
    |--------------------------------------------------------------------------
    |
    | Assigns some optional prefix for the dynamically generated routes from the 
    | configuration file.
    |
    */

    'prefix' => 'select2',

    /*
    |--------------------------------------------------------------------------
    | Middleware for ajax routes
    |--------------------------------------------------------------------------
    |
    | Assign the middleware through which you need your requests to pass in each call
    |
    */

    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Config and publish ajax route
    |--------------------------------------------------------------------------
    |
    | Here you can specify your route settings to make the ajax calls, assigning 
    | the controller, URL patch, name and method to whatever the field calls, via 
    | an alias.
    | 
    | example:
    | 
    | 'alias' => [
    |    'uri' => '/list-select2',
    |    'controller' => \App\Http\Controllers\ExampleController::class,
    |    'method' => 'listSelect2',
    |    'name' => 'list.select2'
    |    'placeholder' => 'Select one',
    |    'showIdOption' => false,
    |    'defaultValue' => null
    | ];
    |
    */

    'routes' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    |
    | You can create your own template to display an error message for validations
    | issued by livewire, the element will be created after the input.
    |
    */

    'error_template' => '<span class="invalid-feedback" role="alert"><strong>:message</strong></span>',
];

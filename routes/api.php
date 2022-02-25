<?php

use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\CompanySettingsController;
use App\Http\Controllers\Api\GraphQLController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InstallationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\StatusController;
use App\Http\Middleware\EnsureIsInstalled;
use Illuminate\Routing\Router;

Route::middleware(EnsureIsInstalled::class)->prefix('auth')->group(static function (Router $router) {

    $router->middleware('auth:sanctum')->group(static function (Router $router) {

        $router->group(['prefix' => 'register'], static function (Router $router) {
            $router->get('{key}', [RegistrationController::class, 'getForm'])->whereUuid('key');
            $router->post('{key}', [RegistrationController::class, 'postForm'])->whereUuid('key');
        });

        $router->post('logout', [AuthController::class, 'logout']);
        $router->post('logout-from-all', [AuthController::class, 'logoutFromAll']);

        $router->get('desktop-key', [AuthController::class, 'issueDesktopKey']);
    });

    $router->group(['prefix' => 'password'], static function (Router $router) {
        $router->post('request', [PasswordResetController::class, 'request']);
        $router->post('validate', [PasswordResetController::class, 'validate']);
        $router->post('process', [PasswordResetController::class, 'process']);
    });

    $router->post('login', [AuthController::class, 'login']);
    $router->post('token', [AuthController::class, 'token']);

    $router->put('desktop-key', [AuthController::class, 'authDesktopKey']);
});

Route::get('status', [StatusController::class, '__invoke']);

Route::group(['prefix' => 'setup'], static function (Router $router) {
    $router->post('database', [InstallationController::class, 'checkDatabaseInfo']);
    $router->put('save', [InstallationController::class, 'save']);
});

Route::middleware([EnsureIsInstalled::class, 'auth:sanctum'])
    ->post('graphql/{schema}', [GraphQLController::class, '__invoke'])
    ->whereAlpha('schema');

Route::any('(.*)', [Controller::class, 'universalRoute']);

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('image', function($app) {
            return new \Intervention\Image\ImageManager();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Image', \Intervention\Image\ImageManager::class);
        View::composer('*', function ($view) {
            $userId = session('user_id');
            if ($userId) {
                // Always fetch fresh from DB so changes reflect immediately
                $user = \App\Models\Users::find($userId);
                if ($user) {
                    $lookups = \App\Models\UserProfPrivileges::where('UserProfileID', $user->UserProfileID)
                        ->join('lookup', 'userprofprivileges.UserPrivilegesID', '=', 'lookup.LookupID')
                        ->select('lookup.LookupName', 'lookup.LookupValue')
                        ->get();

                    $privs   = $lookups->pluck('LookupName')->unique()->toArray();
                    $actions = $lookups->pluck('LookupValue')->toArray();
                } else {
                    $privs   = [];
                    $actions = [];
                }
            } else {
                $privs   = [];
                $actions = [];
            }

            $view->with('actions', $actions);
            $view->with('privs',   $privs);
        });
    }
}

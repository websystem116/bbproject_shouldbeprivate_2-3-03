<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Providers\CustomUserProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // //管理者か
        // Gate::define('isAiphone', function ($user) {
        //     return $user->role == '1';
        // });

        // //会社か
        // Gate::define('isSeko', function ($user) {
        //     return $user->role == '2';
        // });

        // //POSTをアップデートできるのはuser->idが同じ
        // Gate::define('update-post', function ($user, $post) {
        //     return $user->id === $post->user_id;
        // });

        // //POSTを消せるのはuser->idが同じ
        // Gate::define('delete-job', function ($user, $post) {
        //     return $user->id === $post->user_id;
        // });

        Auth::provider('custom', function ($app, array $config) {
            return new CustomUserProvider($app['hash'], $config['model']);
        });
    }
}

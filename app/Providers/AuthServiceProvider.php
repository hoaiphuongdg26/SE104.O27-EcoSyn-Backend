<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Schedule;
use App\Policies\SchedulePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\Post::class => \App\Policies\PostPolicy::class,
        \App\Models\Home::class => \App\Policies\HomePolicy::class,
        \App\Models\Schedule::class => \App\Policies\SchedulePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        Gate::before(function ($user){
            if ($user->hasRole('staff') || $user->hasRole('admin') || $user->hasRole('super admin')){
                return true;
            }
        });
    }
}

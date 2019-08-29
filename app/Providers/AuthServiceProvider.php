<?php

namespace App\Providers;

use App\Policies\CoursePolicy;
use App\Policies\FolderPolicy;
use App\Policies\FilePolicy;
use App\Models\Course;
use App\Models\Folder;
use App\Models\File;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        Course::class => CoursePolicy::class,
        Folder::class => FolderPolicy::class,
        File::class => FilePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
    }
}

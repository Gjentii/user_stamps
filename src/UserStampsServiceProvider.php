<?php

namespace Gjentii\UserStamps;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class UserStampsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Add a macro for user stamps (guard against redefinition)
        if (!Blueprint::hasMacro('userStamps')) {
            Blueprint::macro('userStamps', function () {
                /** @var \Illuminate\Database\Schema\Blueprint $this */
                $this->unsignedBigInteger('created_by')->nullable();
                $this->unsignedBigInteger('updated_by')->nullable();
                $this->unsignedBigInteger('deleted_by')->nullable();
            });
        }

        // Add a macro for dropping user stamps (guard against redefinition)
        if (!Blueprint::hasMacro('dropUserStamps')) {
            Blueprint::macro('dropUserStamps', function () {
                /** @var \Illuminate\Database\Schema\Blueprint $this */
                $this->dropColumn(['created_by', 'updated_by', 'deleted_by']);
            });
        }
    }
}

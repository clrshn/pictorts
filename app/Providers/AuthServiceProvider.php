<?php

namespace App\Providers;

use App\Models\Document;
use App\Models\FinancialRecord;
use App\Policies\DocumentPolicy;
use App\Policies\FinancialRecordPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Document::class => DocumentPolicy::class,
        FinancialRecord::class => FinancialRecordPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Optional: Define gates for additional permissions
        Gate::before(function (User $user, string $ability) {
            // Super admin can do everything
            if ($user->email === 'admin@picto.gov.ph') {
                return true;
            }
        });
    }
}

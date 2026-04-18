<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Interfaces\UsersRepositoryInterface;
use App\Repositories\EloquentUsersRepository;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\EloquentCustomerRepository;
use App\Repositories\Interfaces\LookupRepositoryInterface;
use App\Repositories\EloquentLookupRepository;
use App\Repositories\Interfaces\OrderDetailsRepositoryInterface;
use App\Repositories\EloquentOrderDetailsRepository;
use App\Repositories\Interfaces\OrdersRepositoryInterface;
use App\Repositories\EloquentOrdersRepository;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Repositories\EloquentPaymentRepository;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\EloquentProductRepository;
use App\Repositories\Interfaces\ProductInventoryRepositoryInterface;
use App\Repositories\EloquentProductInventoryRepository;
use App\Repositories\Interfaces\UserProfileRepositoryInterface;
use App\Repositories\EloquentUserProfileRepository;
use App\Repositories\Interfaces\UserProfPrivilegesRepositoryInterface;
use App\Repositories\EloquentUserProfPrivilegesRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UsersRepositoryInterface::class, EloquentUsersRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, EloquentCustomerRepository::class);
        $this->app->bind(LookupRepositoryInterface::class, EloquentLookupRepository::class);
        $this->app->bind(OrderDetailsRepositoryInterface::class, EloquentOrderDetailsRepository::class);
        $this->app->bind(OrdersRepositoryInterface::class, EloquentOrdersRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, EloquentPaymentRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
        $this->app->bind(ProductInventoryRepositoryInterface::class, EloquentProductInventoryRepository::class);
        $this->app->bind(UserProfileRepositoryInterface::class, EloquentUserProfileRepository::class);
        $this->app->bind(UserProfPrivilegesRepositoryInterface::class, EloquentUserProfPrivilegesRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

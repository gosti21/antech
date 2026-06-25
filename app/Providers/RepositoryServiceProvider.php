<?php

namespace App\Providers;

use App\Contracts\Api\v1\Admin\BaseInterface;
use App\Contracts\Api\v1\Admin\BranchInterface;
use App\Contracts\Api\v1\Admin\BranchVariantInterface;
use App\Contracts\Api\v1\Admin\BrandInterface;
use App\Contracts\Api\v1\Admin\CategoryInterface;
use App\Contracts\Api\v1\Admin\CountryInterface;
use App\Contracts\Api\v1\Admin\CoverInterface;
use App\Contracts\Api\v1\Admin\CustomerInterface;
use App\Contracts\Api\v1\Admin\DepartmentInterface;
use App\Contracts\Api\v1\Admin\DistrictInterface;
use App\Contracts\Api\v1\Admin\EmployeeInterface;
use App\Contracts\Api\v1\Admin\MovementInterface;
use App\Contracts\Api\v1\Admin\OptionInterface;
use App\Contracts\Api\v1\Admin\OptionProductInterface;
use App\Contracts\Api\v1\Admin\OptionValueInterface;
use App\Contracts\Api\v1\Admin\OrderInterface;
use App\Contracts\Api\v1\Admin\PaymentMethodInterface;
use App\Contracts\Api\v1\Admin\ProductInterface;
use App\Contracts\Api\v1\Admin\ProvinceInterface;
use App\Contracts\Api\v1\Admin\SaleInterface;
use App\Contracts\Api\v1\Admin\ShipmentInterface;
use App\Contracts\Api\v1\Admin\ShippingCompanyInterface;
use App\Contracts\Api\v1\Admin\SpecificationInterface;
use App\Contracts\Api\v1\Admin\SubcategoryInterface;
use App\Contracts\Api\v1\Admin\VariantInterface;
use App\Contracts\Api\v1\Auth\AuthInterface;
use App\Repositories\Api\v1\Admin\BaseRepository;
use App\Repositories\Api\v1\Admin\BranchRepository;
use App\Repositories\Api\v1\Admin\BranchVariantRepository;
use App\Repositories\Api\v1\Admin\BrandRepository;
use App\Repositories\Api\v1\Admin\CategoryRepository;
use App\Repositories\Api\v1\Admin\CountryRepository;
use App\Repositories\Api\v1\Admin\CoverRepository;
use App\Repositories\Api\v1\Admin\CustomerRepository;
use App\Repositories\Api\v1\Admin\DepartmentRepository;
use App\Repositories\Api\v1\Admin\DistrictRepository;
use App\Repositories\Api\v1\Admin\EmployeeRepository;
use App\Repositories\Api\v1\Admin\MovementRepository;
use App\Repositories\Api\v1\Admin\OptionProductRepository;
use App\Repositories\Api\v1\Admin\OptionRepository;
use App\Repositories\Api\v1\Admin\OptionValueRepository;
use App\Repositories\Api\v1\Admin\OrderRepository;
use App\Repositories\Api\v1\Admin\PaymentMethodRepository;
use App\Repositories\Api\v1\Admin\ProductRepository;
use App\Repositories\Api\v1\Admin\ProvinceRepository;
use App\Repositories\Api\v1\Admin\SaleRepository;
use App\Repositories\Api\v1\Admin\ShipmentRepository;
use App\Repositories\Api\v1\Admin\ShippingCompanyRepository;
use App\Repositories\Api\v1\Admin\SpecificationRepository;
use App\Repositories\Api\v1\Admin\SubcategoryRepository;
use App\Repositories\Api\v1\Admin\VariantRepository;
use App\Repositories\Api\v1\Auth\AuthRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AuthInterface::class, AuthRepository::class);
        $this->app->bind(BaseInterface::class, BaseRepository::class);
        $this->app->bind(CategoryInterface::class, CategoryRepository::class);
        $this->app->bind(SubcategoryInterface::class, SubcategoryRepository::class);
        $this->app->bind(BrandInterface::class, BrandRepository::class);
        $this->app->bind(ProductInterface::class, ProductRepository::class);
        $this->app->bind(CoverInterface::class, CoverRepository::class);
        $this->app->bind(SpecificationInterface::class, SpecificationRepository::class);
        $this->app->bind(OptionInterface::class, OptionRepository::class);
        $this->app->bind(OptionValueInterface::class, OptionValueRepository::class);
        $this->app->bind(OptionProductInterface::class, OptionProductRepository::class);
        $this->app->bind(BranchInterface::class, BranchRepository::class);
        $this->app->bind(VariantInterface::class, VariantRepository::class);
        $this->app->bind(MovementInterface::class, MovementRepository::class);
        $this->app->bind(BranchVariantInterface::class, BranchVariantRepository::class);
        $this->app->bind(CountryInterface::class, CountryRepository::class);
        $this->app->bind(DepartmentInterface::class, DepartmentRepository::class);
        $this->app->bind(ProvinceInterface::class, ProvinceRepository::class);
        $this->app->bind(DistrictInterface::class, DistrictRepository::class);
        $this->app->bind(PaymentMethodInterface::class, PaymentMethodRepository::class);
        $this->app->bind(SaleInterface::class, SaleRepository::class);
        $this->app->bind(EmployeeInterface::class, EmployeeRepository::class);
        $this->app->bind(CustomerInterface::class, CustomerRepository::class);
        $this->app->bind(OrderInterface::class, OrderRepository::class);
        $this->app->bind(ShipmentInterface::class, ShipmentRepository::class);
        $this->app->bind(ShippingCompanyInterface::class, ShippingCompanyRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

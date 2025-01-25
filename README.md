# Laravel Repository Pattern

[![Latest Version on Packagist](https://img.shields.io/packagist/v/madulinux/repository-pattern.svg?style=flat-square)](https://packagist.org/packages/madulinux/repository-pattern)
[![Total Downloads](https://img.shields.io/packagist/dt/madulinux/repository-pattern.svg?style=flat-square)](https://packagist.org/packages/madulinux/repository-pattern)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

A flexible and easy-to-use Repository Pattern implementation for Laravel applications. This package helps you implement the repository pattern in your Laravel applications with minimal setup.

## Features

- 🚀 Quick repository generation with artisan command
- 📦 Base Repository with common CRUD operations
- 🔍 Built-in search and filter functionality
- 🛠 Customizable repository stubs
- 💡 Support for API resources and form requests
- ⚡️ Caching support

## Requirements

- PHP ^8.2
- Laravel ^10.0|^11.0

## Installation

You can install the package via composer:

```bash
composer require madulinux/repository-pattern
```

## Basic Usage

### Generate a Repository

```bash
# Generate basic repository
php artisan make:pattern User

# Generate with model, repository, and service
php artisan make:pattern User -mrs

# Generate with API resources and requests
php artisan make:pattern User --all --api
```

### Using the Repository

```php
use App\Repositories\UserRepository;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        return $this->userRepository->all();
    }

    public function show($id)
    {
        return $this->userRepository->find($id);
    }
}
```

## Available Methods

The base repository includes these common methods:

- `all()`: Get all records
- `find($id)`: Find record by ID
- `findOrFail($id)`: Find record or fail
- `create(array $data)`: Create new record
- `update($id, array $data)`: Update record
- `delete($id)`: Delete record
- `paginate($perPage = 15)`: Get paginated records
- `search(string $query, array $columns)`: Search records
- `searchPaginated(string $query, array $params)`: Search with pagination
- `getFiltered(array $filters, array $params)`: Get filtered records

## Caching

The package includes built-in caching support through the `Cacheable` trait. Here's how to use it:

```php
use MaduLinux\RepositoryPattern\Repository;

class UserRepository extends Repository
{
    // Cache is enabled by default with 60 minutes duration
    
    // Optionally customize cache settings
    protected $cacheTime = 120; // 2 hours
    protected $cacheTags = ['users']; // Cache tags for better organization
    
    // Your repository methods...
}
```

### Cache Control Methods

```php
// Disable caching
$repository->disableCache();

// Enable caching
$repository->enableCache();

// Set cache duration
$repository->setCacheTime(30); // 30 minutes

// Set cache tags
$repository->setCacheTags(['users', 'active']);

// Manually flush cache
$repository->flushCache();
```

### Cache Implementation

- All read operations are automatically cached
- Create, update, and delete operations automatically flush the cache
- Cache keys are generated based on method name and parameters
- Uses Laravel's cache system with support for different cache drivers
- Supports cache tags when using Redis or Memcached

### Cache Configuration

The caching feature uses Laravel's cache configuration. Make sure you have configured your cache driver in `config/cache.php`.

For cache tags support, you need to use Redis or Memcached as your cache driver:

```php
// config/cache.php
return [
    'default' => env('CACHE_DRIVER', 'redis'),
    
    'stores' => [
        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
        ],
        // ...
    ],
];
```

## Customization

### Custom Repository

```php
namespace App\Repositories;

use App\Models\User;
use MaduLinux\RepositoryPattern\Repository;

class UserRepository extends Repository
{
    protected $model = User::class;

    // Add your custom methods here
    public function activeUsers()
    {
        return $this->model->where('status', 'active')->get();
    }
}
```

### Custom Stubs

You can publish the stubs to customize the generated files:

```bash
php artisan vendor:publish --provider="MaduLinux\RepositoryPattern\RepositoryPatternServiceProvider" --tag="stubs"
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email madulinux@gmail.com instead of using the issue tracker.

## Credits

- [Madulinux](https://github.com/madulinux)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

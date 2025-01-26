# Laravel Repository Pattern

A flexible and feature-rich implementation of the Repository Pattern for Laravel applications.

## Features

- 🚀 **CRUD Operations** - Basic create, read, update, and delete operations
- 💾 **Caching Support** - Automatic caching with customizable drivers and keys
- 🔄 **Event System** - Before/After events for all operations
- 🗑️ **Soft Deletes** - Built-in support for soft deletes
- 📦 **Bulk Operations** - Efficient handling of multiple records
- 💼 **Transactions** - Database transaction support
- 🔍 **Query Builder** - Chainable query methods
- 📄 **Pagination** - Built-in pagination support
- 🔎 **Search** - Simple and advanced search capabilities

## Installation

```bash
composer require madulinux/repository-pattern
```

## Basic Usage

### Generate Repository

```bash
php artisan make:repository User
```

This will create:
- `app/Repositories/UserRepository.php`
- `app/Repositories/Interfaces/UserRepositoryInterface.php`

### Basic Operations

```php
class UserRepository extends Repository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }
}

// Usage
$users = $repository->all();
$user = $repository->find(1);
$user = $repository->create(['name' => 'John']);
$repository->update(1, ['name' => 'Jane']);
$repository->delete(1);
```

### Caching

```php
// Enable/Disable cache
$repository->enableCache();
$repository->disableCache();

// Set cache time
$repository->setCacheTime(60); // 60 minutes

// Set cache tags (for Redis/Memcached)
$repository->setCacheTags(['users']);

// Custom cache key
$repository->setCacheKeyGenerator(function($method, $args, $model) {
    return "users:{$method}:" . md5(serialize($args));
});
```

### Events

```php
$repository->on('creating', function($data) {
    // Before create
});

$repository->on('created', function($model) {
    // After create
});

// Available events:
// - creating/created
// - updating/updated
// - deleting/deleted
```

### Soft Deletes

```php
// Get with soft deleted records
$repository->withTrashed()->get();

// Get only soft deleted records
$repository->onlyTrashed()->get();

// Restore soft deleted records
$repository->restore($id);

// Force delete
$repository->forceDelete($id);
```

### Bulk Operations

```php
// Insert multiple
$repository->insert([
    ['name' => 'John'],
    ['name' => 'Jane']
]);

// Update multiple
$repository->bulkUpdate(
    ['status' => 'active'],
    ['department' => 'IT']
);

// Delete multiple
$repository->bulkDelete([1, 2, 3]);

// Upsert
$repository->upsert(
    [['id' => 1, 'name' => 'John']],
    'id',
    ['name']
);

// Chunk processing
$repository->chunk(100, function($users) {
    foreach($users as $user) {
        // Process each user
    }
});
```

### Transactions

```php
// Using callback
$repository->transaction(function() use ($repository) {
    $user = $repository->create(['name' => 'John']);
    $profile = $repository->create(['user_id' => $user->id]);
});

// Manual control
$repository->beginTransaction();
try {
    $user = $repository->create(['name' => 'John']);
    $profile = $repository->create(['user_id' => $user->id]);
    $repository->commit();
} catch (\Exception $e) {
    $repository->rollBack();
    throw $e;
}
```

### Query Builder

```php
// Chainable methods
$repository
    ->with(['posts', 'comments'])
    ->filter(['status' => 'active'])
    ->orderBy('created_at', 'desc')
    ->paginate(15);

// Search
$users = $repository->search('john', ['name', 'email']);

// Advanced filtering
$users = $repository->getFiltered([
    'status' => 'active',
    'role' => ['admin', 'manager'],
    'age' => ['operator' => '>=', 'value' => 18]
], [
    'sort_by' => 'created_at',
    'sort_direction' => 'desc',
    'per_page' => 15
]);
```

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email security@example.com instead of using the issue tracker.

## Credits

- [Author Name](https://github.com/username)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

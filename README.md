# Laravel UserStamps

Blueprint macros that add `created_by`, `updated_by`, and `deleted_by` columns to your migrations, plus a trait that auto-fills them on create/update/delete/restore.

## Installation

1) Require the package (published under `Gjentii`):

```bash
composer require gjentii/laravel-userstamps
```

For local development in an existing app without publishing to Packagist yet, add a path repository in your root `composer.json` (point the `url` to where this package folder lives relative to your app):

```json
{
  "repositories": [
    { "type": "path", "url": "../user_stamps" }
  ]
}
```

Examples: `../user_stamps`, `../packages/laravel-userstamps`, or any relative/absolute path that matches your folder structure.

Then require it as a normal semver version (this package declares `0.1.0` for local path installs):

```bash
composer require gjentii/laravel-userstamps:^0.1.0
```

The package uses Laravel auto-discovery. No manual provider registration needed.

Troubleshooting Composer stability:
- With the `version` field present, the package resolves as a stable release for path installs. If you still see stability errors:
  - Ensure the consuming app defines the path repository correctly and runs `composer update -W` once.
  - Alternatively, tag a git version (e.g., `v0.1.0`) and require that tag.

## Usage

In any migration, call the macros on the Blueprint `$table`:

```php
Schema::create('things', function (Blueprint $table) {
    $table->id();
    // ... your columns
    $table->userStamps(); // adds created_by, updated_by, deleted_by (nullable, unsignedBigInteger)
    $table->timestamps();
});
```

To drop them in `down()` or later migrations:

```php
$table->dropUserStamps();
```

Attach the `UserStamps` trait to any Eloquent model you want to auto-populate the columns:

```php
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // if applicable
use Gjentii\UserStamps\Traits\UserStamps;

class Post extends Model
{
    use SoftDeletes; // optional, but enables deleted_by handling
    use UserStamps;  // auto-sets created_by, updated_by, deleted_by
}
```

Behavior:
- On `creating`: sets `created_by` and `updated_by` to `Auth::id()` if authenticated
- On `updating`: sets `updated_by` to `Auth::id()` if authenticated
- On `deleting` with SoftDeletes: sets `deleted_by` to `Auth::id()` and saves quietly
- On `restoring` with SoftDeletes: clears `deleted_by`

Relationships included on the trait:

```php
$model->createdBy();
$model->updatedBy();
$model->deletedBy();
```

The user model class is resolved from `config('auth.providers.users.model')` (falls back to `App\\Models\\User`).

## Namespacing

If you prefer a different namespace/vendor, change these values:

- `composer.json` -> `name`: `your-username/laravel-userstamps`
- `composer.json` -> `autoload.psr-4`: `Gjentii\\UserStamps\\`
- `src/UserStampsServiceProvider.php` -> namespace `Gjentii\\UserStamps`
- `src/Traits/UserStamps.php` -> namespace `Gjentii\\UserStamps\\Traits`

Then run `composer dump-autoload`.

## License

MIT

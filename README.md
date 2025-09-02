# Laravel UserStamps

Blueprint macros that add `created_by`, `updated_by`, and `deleted_by` columns to your migrations, plus a trait that auto-fills them on create/update/delete/restore.

## Installation

If you see “could not be found in any version,” Composer cannot locate the package on Packagist. Use one of these options:

1) Local path (fastest for development)

Add a path repository in your app’s `composer.json` (the `url` points to where this package folder lives relative to your app):

```json
{
  "repositories": [
    { "type": "path", "url": "../user_stamps" }
  ]
}
```

Examples: `../user_stamps`, `../packages/laravel-userstamps`, or any relative/absolute path that matches your folder structure.

Then require it as a normal semver version (this package declares `0.1.0` for path installs):

```bash
composer require gjentii/laravel-userstamps:^0.1.0
```

2) Git/VCS repository (no Packagist)

Push this repo to Git (GitHub, GitLab, etc.) and add a VCS repository in your app’s `composer.json`:

```json
{
  "repositories": [
    { "type": "vcs", "url": "https://github.com/your-username/laravel-userstamps.git" }
  ]
}
```

- Preferred: create a tag in this repo, e.g. `v0.1.0`, then:

```bash
composer require gjentii/laravel-userstamps:^0.1.0
```

- If you haven’t tagged yet, require the branch and allow dev stability:

```bash
composer require gjentii/laravel-userstamps:dev-main --no-update
composer config minimum-stability dev
composer config prefer-stable true
composer update gjentii/laravel-userstamps -W
```

Alternative: alias the branch as a stable version without changing your project-wide stability:

```json
{
  "require": {
    "gjentii/laravel-userstamps": "dev-main as 0.1.0"
  }
}
```

3) Publish to Packagist (recommended for public use)

- Push the repo to a public Git remote.
- Create a Git tag: `v0.1.0` (Composer derives versions from tags).
- Submit the repository URL on `https://packagist.org/packages/submit`.
- Then in your app:

```bash
composer require gjentii/laravel-userstamps
```

The package uses Laravel auto-discovery. No manual provider registration needed.

Troubleshooting Composer stability:
- Path installs use the `version` field in this package and resolve as stable.
- VCS/Packagist installs ignore the `version` field and use Git tags. Tag a release (e.g., `v0.1.0`) or require `dev-main` with appropriate stability settings as shown above.

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

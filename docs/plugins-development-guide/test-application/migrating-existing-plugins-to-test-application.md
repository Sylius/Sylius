# Migrating existing plugins to Test Application

In older versions of Sylius plugins, each plugin included a full Sylius + Symfony application in a `tests/Application` directory. This local test app was used to run automated tests against the plugin.

While functional, this setup had serious drawbacks:

* The entire Sylius app was duplicated in every plugin
* Updates to Sylius core required manual syncing
* Config drift and inconsistencies caused debugging issues
* Setting it up required deep Symfony knowledge

With Sylius 2.x, this approach is considered outdated. Plugins should now use the **shared Test Application** provided by the `sylius/test-application` package. It's faster to set up, easier to maintain, and works out of the box with CI.

This guide walks you step by step through migrating your plugin.

***

### 1. Install the Test Application Package

Require the package:

```bash
composer require --dev sylius/test-application:^2.0.0@alpha
```

***

### 2. Create Plugin-Specific Configuration

Make a new directory:

```bash
mkdir -p tests/TestApplication/config
```

Create `.env`:

```env
DATABASE_URL=mysql://root@127.0.0.1/test_application_%kernel.environment%

SYLIUS_TEST_APP_CONFIGS_TO_IMPORT="@YourPlugin/config/config.yaml"
SYLIUS_TEST_APP_ROUTES_TO_IMPORT="@YourPlugin/config/routes.yaml"
SYLIUS_TEST_APP_BUNDLES_PATH="tests/TestApplication/config/bundles.php"

# Optionally, replace the default bundles entirely
SYLIUS_TEST_APP_BUNDLES_REPLACE_PATH="tests/TestApplication/config/bundles.php"
# Optionally, use a semicolon-separated list to add needed bundles
SYLIUS_TEST_APP_BUNDLES_TO_ENABLE="Your\Bundle\Namespace\Plugin"
```

📌 **What these variables do:**

* `SYLIUS_TEST_APP_CONFIGS_TO_IMPORT`: Loads your plugin config
* `SYLIUS_TEST_APP_ROUTES_TO_IMPORT`: Registers your plugin routes
* `SYLIUS_TEST_APP_BUNDLES_PATH`: Loads only bundles specific to your plugin
* `SYLIUS_TEST_APP_BUNDLES_REPLACE_PATH`: Replaces the default bundles entirely
* `SYLIUS_TEST_APP_BUNDLES_TO_ENABLE`: Loads only bundles specific to your plugin using a semicolon-separated list

***

### 3. Register Bundles (if needed)

Create `tests/TestApplication/config/bundles.php`:

```php
<?php

return [
    Some\Dependency\Bundle::class => ['all' => true],
    Your\Bundle\Namespace\Plugin::class => ['all' => true],
];
```

This works like Symfony’s main `bundles.php` and is only needed if `SYLIUS_TEST_APP_BUNDLES_PATH` is used.

***

### 4. Add Plugin Configuration

In `tests/TestApplication/config/config.yaml`:

```yaml
imports:
    - { resource: "@YourPlugin/config/config.yaml" }
    - { resource: "services_test.php" }

parameters:
    your_plugin_param: '%kernel.project_dir%/../../../config/plugin_param/'

twig:
    paths:
        '%kernel.project_dir%/../../../tests/TestApplication/templates': ~
```

Create `services_test.php` to load test-only services:

```php
<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $env = $_ENV['APP_ENV'] ?? 'dev';

    if (str_starts_with($env, 'test')) {
        $container->import('../../../vendor/sylius/sylius/src/Sylius/Behat/Resources/config/services.xml');
        $container->import('@YourPlugin/tests/Behat/Resources/services.xml');
    }
};
```

***

### 5. Add JavaScript Integration

Create a symlink to `node_modules` in Test Application:

```bash
ln -s vendor/sylius/test-application/node_modules node_modules
```

Create minimal controller definitions:

```json
// YourPlugin/assets/shop/controllers.json
{
  "controllers": [],
  "entrypoints": []
}

// YourPlugin/assets/admin/controllers.json
{
  "controllers": [],
  "entrypoints": []
}
```

Optionally extend `tests/TestApplication/package.json` with extra dependencies if  your plugin requires additional JavaScript dependencies. You can also remove existing dependencies from the default `package.json.dist` using the `removeDependencies` and `removeDevDependencies` keys:

```json
{
  "dependencies": {
    "trix": "^2.0.0"
  },
  "devDependencies": {
    "swiper": "^11.2.6"
  },
  "removeDependencies": [
    "@symfony/ux-autocomplete"
  ],
  "removeDevDependencies": [
    "tom-select"
  ]
}
```

This file will be merged during `yarn install` with the main TestApplication `package.json`, while any packages listed under `removeDependencies` or `removeDevDependencies` will be omitted.

***

### 6. Handle Entity Extensions (If Needed)

If your plugin overrides Sylius entities:

* Place extended classes in `tests/TestApplication/src/Entity`
* Register the mapping in `config.yaml` or `doctrine.yaml`:

```yaml
doctrine:
    orm:
        entity_managers:
            default:
                mappings:
                    TestApplication:
                        is_bundle: false
                        type: attribute
                        dir: '%kernel.project_dir%/../../../tests/TestApplication/src/Entity'
                        prefix: Tests\Your\Plugin\Namespace
```

Update `composer.json`:

```json
"autoload-dev": {
    "psr-4": {
        "Tests\\Your\\Plugin\\Namespace\\": "tests/TestApplication/src/"
    }
}
```

Then dump autoload:

```bash
composer dump-autoload
```

***

### 7. Define Public Directory

Configure public directory in your `composer.json`:

```json
"extra": {
    "public-dir": "vendor/sylius/test-application/public"
}
```

***

### 8. Ignore Local Overrides And /var Directory

In your `.gitignore`:

```
/tests/TestApplication/.env.local
/tests/TestApplication/.env.*.local
/var/
```

***

The default recommended structure of the Test Application and related directories should look like this:

```
your_plugin/
├─ assets/
│  ├─ admin/
│  │  └─ controllers.json
│  └─ shop/
│     └─ controllers.json
├─ node_modules/ (symlink)
├─ tests/
│  └─ TestApplication/
│     ├─ config/
│     │  ├─ config.yaml
│     │  ├─ routes.yaml
│     │  ├─ services.yaml
│     │  └─ services_test.php
│     ├─ src/
│     │  └─ Entity/
│     │     └─ [Your custom entities if needed]
│     ├─ templates/
│     │  └─ .gitkeep
│     ├─ .env
│     ├─ .env.local (not committed)
│     ├─ .env.test
│     ├─ .env.test.local (not committed)
│     ├─ bundles.php
│     └─ package.json
└─ var
   ├─ cache/
   └─ log/
```

***

That’s it! Your plugin is now migrated and is using the new Test Application.\
For new plugins, check out the [Plugin Setup Guide](creating-and-testing-plugins-using-test-application.md).

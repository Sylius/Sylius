# Test Application

The **Test Application** is a shared testing environment designed to simplify Sylius plugin development. Instead of setting up a full application in every plugin, you now use a common, pre-configured application maintained by the Sylius team.

This guide introduces what the Test Application is, why it's useful, and how it changes plugin development. From here, you'll learn how to:

* Migrate an existing plugin to use the new structure
* Set up a brand new plugin with the TestApplication from the start

### Why This Change?

Previously, each plugin contained its own Symfony application under `tests/Application`, leading to duplicated code and setup. The new approach uses a centralized package:

* Shared base in `vendor/sylius/test-application`
* Plugin-specific config in `tests/TestApplication/`
* Simpler updates via Composer

This reduces maintenance, improves consistency, and speeds up development.

### Key Benefits

✅ No duplicate applications to maintain\
✅ Minimal plugin-specific setup\
✅ Faster upgrades and consistent environments\
✅ Predictable and reliable testing across all plugins\
✅ CI-friendly: works the same locally and in automation\
✅ Easy onboarding for contributors, less setup overhead\
✅ Supports PHPUnit, Behat, Doctrine migrations, and asset compilation

### How to Set It Up

1.  Install the package:

    ```bash
    composer require --dev sylius/test-application:^2.0.0@alpha
    ```
2.  Add your plugin’s configuration:

    ```bash
    mkdir -p tests/TestApplication/config
    # Add your services.yaml and routes.yaml
    ```
3.  Set environment variables in `.env`:

    ```
    DATABASE_URL=mysql://root@127.0.0.1/test_application_%kernel.environment%

    SYLIUS_TEST_APP_CONFIGS_TO_IMPORT="@YourPlugin/config/config.yaml"
    SYLIUS_TEST_APP_ROUTES_TO_IMPORT="@YourPlugin/config/routes.yaml"
    SYLIUS_TEST_APP_BUNDLES_PATH="tests/TestApplication/config/bundles.php"

    # Optionally, replace the default bundles entirely
    SYLIUS_TEST_APP_BUNDLES_REPLACE_PATH="tests/TestApplication/config/bundles.php"
    # Optionally, use a semicolon-separated list to add needed bundles
    SYLIUS_TEST_APP_BUNDLES_TO_ENABLE="Your\Bundle\Namespace"
    ```
4.  Configure your plugin's bundles in `tests/TestApplication/config/bundles.php` if needed:

    ```php
    <?php

    return [
        Some\Dependency\Bundle::class => ['all' => true],
        Your\Bundle\Namespace\Plugin::class => ['all' => true],
    ];
    ```

No need for a custom Symfony kernel or app skeleton.

***

### Next: What Do You Want to Do?

👉 **Already have a plugin using `tests/Application`?** Learn how to switch to the new Test Application structure in [the Migration Guide](migrating-existing-plugins-to-test-application.md).

👉 **Starting a brand new plugin?** Jump straight into [the Plugin Setup Guide](creating-and-testing-plugins-using-test-application.md) for a clean setup using the Test Application.

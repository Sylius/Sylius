# How to Create a Plugin for Sylius?

A Sylius plugin is simply a standard **Symfony bundle** that extends the core Sylius behavior. The recommended way to start developing your own plugin is by using the official plugin skeleton:

> 🔗 [Sylius Plugin Skeleton](https://github.com/Sylius/PluginSkeleton)

It provides built-in infrastructure for development, testing, and behavior-driven development (BDD) using tools like PHPUnit and Behat.

## Quickstart Guide

{% stepper %}
{% step %}
#### Create a plugin project

```bash
composer create-project sylius/plugin-skeleton ShopUserCleanupPlugin
cd ShopUserCleanupPlugin
```
{% endstep %}

{% step %}
#### Test the plugin via the built-in Test Application

The Plugin Skeleton includes a ready-to-use **TestApplication** provided by the `sylius/test-application` package. This is a minimal Sylius application used for testing and previewing your plugin in isolation.

{% hint style="info" %}
You can read more about the test application here: [Broken link](broken-reference "mention")
{% endhint %}

You can initialize it with:

```bash
composer test-app-init
```

This command:

* Creates the database and runs migrations
* Builds frontend assets
* Loads fixtures

Once initialized, you can run the application with Symfony CLI:

```bash
symfony serve -d
```

Then open [https://127.0.0.1:8000](https://127.0.0.1:8000/) in your browser.

This setup allows you to quickly verify your plugin behavior in a working Sylius instance without integrating it into another project.
{% endstep %}

{% step %}
#### Clean up the example skeleton content

The plugin skeleton includes greeting behavior and placeholder features that you’ll typically want to remove.

{% hint style="info" %}
If you're using Claude or a similar AI assistant, you can automate using `CLEANUP_GUIDE.md` context:

```bash
claude
> Use CLEANUP_GUIDE.md to remove all initial customizations from a Sylius Plugin Skeleton
```
{% endhint %}
{% endstep %}

{% step %}
#### Rename the plugin to reflect its purpose and vendor

The Plugin Skeleton comes with a generic namespace: `Acme\SyliusExamplePlugin`. You'll want to rename it to match your actual plugin name and vendor. For example: `Acme\SyliusShopUserCleanupPlugin`.

{% hint style="info" %}
If you're using Claude or a similar AI assistant, you can automate using `RENAME_GUIDE.md` context:

```bash
claude
> Use RENAME_GUIDE.md to change the plugin name to "ShopUserCleanupPlugin" in the organization "Acme"
```
{% endhint %}
{% endstep %}

{% step %}
#### Declare Sylius compatibility

Your plugin should explicitly define which versions of Sylius it supports. Add a clear version constraint in `composer.json`:&#x20;

```json
"require": {
    "sylius/sylius": "^2.0"
}
```

to avoid installation in incompatible environments. Make sure to test your plugin’s behaviour across the intended Sylius versions using the built-in **Test Application**.&#x20;

{% hint style="info" %}
If you plan to support both **Sylius 1.14** and **2.0,** you can use the `COMPATIBILITY_GUIDE.md` file to automate and verify support for multiple Sylius versions.&#x20;
{% endhint %}
{% endstep %}

{% step %}
#### Implement your plugin functionality

With your plugin cleaned up and renamed, you're ready to start implementing real features.
{% endstep %}
{% endstepper %}

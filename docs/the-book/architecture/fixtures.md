---
layout:
  title:
    visible: true
  description:
    visible: false
  tableOfContents:
    visible: true
  outline:
    visible: true
  pagination:
    visible: true
---

# Fixtures

Fixtures are used mainly for testing, but also for having your shop in a certain state, and having defined data - they ensure that there is a fixed environment in which your application is working.

Note

The way Fixtures are designed in Sylius is well described in the [FixturesBundle documentation](https://github.com/Sylius/SyliusFixturesBundle/blob/master/docs/index.md).

### What are the available fixtures in Sylius?

To check what fixtures are defined in Sylius run:

```bash
php bin/console sylius:fixtures:list
```

### How to load Sylius fixtures?

The recommended way to load the predefined set of Sylius fixtures is here:

```bash
php bin/console sylius:fixtures:load
```

### What data is loaded by fixtures in Sylius?

All files that serve for loading fixtures of Sylius are placed in the `Sylius/Bundle/CoreBundle/Fixture/*` directory.

And the specified data for fixtures is stored in the [Sylius/Bundle/CoreBundle/Resources/config/app/fixtures.yml](https://github.com/Sylius/Sylius/blob/2.0/src/Sylius/Bundle/CoreBundle/Resources/config/app/fixtures.yml) file.

### Available configuration options

#### locale

| Configuration key     | Function                                                                                            |
| --------------------- | --------------------------------------------------------------------------------------------------- |
| load\_default\_locale | Determine if default shop locale (defined as _%locale%_) parameter will be loaded. True by default. |
| locales               | Array of locale codes, which will be loaded. Empty by default.                                      |

### Learn more

* [FixturesBundle documentation](https://github.com/Sylius/SyliusFixturesBundle/blob/master/docs/index.md)

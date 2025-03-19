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

# Upgrading Sylius CE

Sylius regularly releases new versions according to our Release Cycle. Each minor release comes with an `UPGRADE.md` file, which is meant to help in the upgrading process.

1. Update the Sylius library version constraint by modifying the `composer.json` file:

```json
{ 
    "require": { 
        "sylius/sylius": "^2.0" 
    } 
} 
```

2. Upgrade dependencies by running a Composer command:

```bash
composer update sylius/sylius --with-all-dependencies 
```

If this does not help, it is a matter of debugging the conflicting versions and working out how your `composer.json` should look after the upgrade. You can check what version of Sylius is installed by running `composer show sylius/sylius` command.

Follow the instructions found in the `UPGRADE.md` file for a given minor release.

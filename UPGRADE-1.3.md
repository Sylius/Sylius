# UPGRADE FROM `v1.2.X` TO `v1.3.0`

## Configuration

* **Sylius 1.3** requires **PHP 7.2**, then following configuration should be changed:
    * `composer.json`:
        * `"php": "^7.1"` -> `"php": "^7.2"`
        * `"config.platform.php": "7.1.18"` -> `"config.platform.php": "7.2.4"`
    * `.travis.yml` (if you're using Travis CI):
        * leave only configuration for `PHP 7.2`

* Copy [.env.dist from Sylius 1.3](https://github.com/Sylius/Sylius-Standard/blob/1.3/.env.dist) to your application root:
    * Basing on this file create env files for your environments (`.env`, `.env.test` etc.)
    * Add `.env` and `.env.test` files to `.gitignore` (or any other not-dist files you've created in a previous step)
    * Configure environmental variables (especially `DATABASE_URL`)

* If you want to reuse Sylius Behat contexts, it could be required to run
`composer require friends-of-behat/page-object-extension —-dev` which is now used in Sylius test suite

## Catalog structure change

**Sylius 1.3** uses new directory structure, introduced in **Symfony 4**. Even though it's not required,
we strongly encourage you to follow this convention. Take a look at
[official Symfony upgrade documentation](symfony.com/doc/current/setup/flex.html#upgrading-existing-applications-to-flex),
to know what exactly should be done.

#### Remember!

After upgrading the catalog structure, copy [public/index.php](https://github.com/Sylius/Sylius-Standard/blob/1.3/public/index.php)
and [bin/console](https://github.com/Sylius/Sylius-Standard/blob/1.3/bin/console) files from **Sylius-Standard**
to make the whole process completed.

# UPGRADE FROM `v1.2.X` TO `v1.3.0`

## Configuration

* **Sylius 1.3** requires **PHP 7.2**, then following configuratio should be changed:
    * `composer.json`:
        * `"php": "^7.1"` -> `"php": "^7.2"`
        * `"config.platform.php": 7.1.18` -> `"config.platform.php": 7.2.4`
    * `.travis.yml` (if you're using TravisCI):
        * leave only configuration for `PHP 7.2`

* Copy `vendor/sylius/sylius/env.dist` to your application root:
    * basing on this file create env files for your environments (`.env`, `.env.test` etc.)
    * Add env files to `.gitignore`
    * Configure environmental variables (especially `DATABASE_URL`)

* If you want to reuse Sylius Behat contexts, it could be required to run
`composer require friends-of-behat/page-object-extension —-dev` which is now used in Sylius test suit


## Catalog structure change

**Sylius 1.3** uses new directory structure, introduced in **Symfony 4**. Even though it's not required,
we strongly encourage you to follow this convention. Here is a quick tutorial how to make it fast and 
efficient.

### TODO :)

# UPGRADE FROM `v1.6.X` TO `v1.7.0`

1. Require upgraded Sylius version using Composer:

    ```bash
    composer require sylius/sylius:~1.7.0
    ```
   
   You might need to adjust your `config.platform.php` setting in `composer.json`, because Sylius 1.7 requires PHP 7.3 or higher.

2. Update your `package.json` in order to add new dependencies: 

    ```diff
    {
      "dependencies": {
    +   "chart.js": "^2.9.3",
    +   "slick-carousel": "^1.8.1",
      },
      "devDependencies": {
    +   "@symfony/webpack-encore": "^0.28.0",
    +   "sass-loader": "^7.0.1",
      }
      ...
    }
    ```
   
   Run `yarn install && yarn build` to use them.

3. Remove `SonataCoreBundle` from your list of used bundles in `config/bundles.php` if you are not using it apart from Sylius:

    ```diff
    -   Sonata\CoreBundle\SonataCoreBundle::class => ['all' => true],
    ```
    
    You should remove `config/packages/sonata_core.yaml` as well.
    
4. Remove `config/packages/twig_extensions.yaml` file if you are not using the `twig/extensions` package in your application.
    
5. Add the following snippet to `config/packages/twig.yaml` to enable `Twig\Extra\Intl\IntlExtension`:

    ```yaml
    services:
        _defaults:
            public: false
            autowire: true
            autoconfigure: true
            
        Twig\Extra\Intl\IntlExtension: ~
   ```
   
6. Copy migration files into `src/Migrations`:

    - [Version20191119131635.php](https://raw.githubusercontent.com/Sylius/Sylius-Standard/1.7/src/Migrations/Version20191119131635.php)
    - [Version20200301170604.php](https://raw.githubusercontent.com/Sylius/Sylius-Standard/1.7/src/Migrations/Version20200301170604.php)
    
    Run `bin/console doctrine:migrations:migrate` to use them.
    
7. Clear cache by `bin/console cache:clear`.

## Template events

- `Sylius\Bundle\UiBundle\Block\BlockEventListener` has been deprecated, use `sylius_ui` configuration instead.

## Breaking changes

Those are excluded from our BC promise:

- `Sylius\Bundle\ShopBundle\EventListener\UserMailerListener` has been removed and replaced with `Sylius\Bundle\CoreBundle\EventListener\MailerListener`

## Billing and shipping addresses have been switched with one another

Until now shipping address used to be the default address of an Order. We have changed that, so now the billing address 
became the default address during checkout. It is an important change in our checkout process, please have that in mind.

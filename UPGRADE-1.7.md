# UPGRADE FROM `v1.7.4` TO `v1.7.5`

We've brought back the attribute types templates to SyliusAttributeBundle after BC break in v1.7.0.

# UPGRADE FROM `v1.6.X` TO `v1.7.0`

1. Require upgraded Sylius version using Composer:

    ```bash
    composer require sylius/sylius:~1.7.0 --update-with-dependencies
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

3. Remove not needed bundles from your list of used bundles in `config/bundles.php` if you are not using it apart from Sylius:

    ```diff
    -   Sonata\CoreBundle\SonataCoreBundle::class => ['all' => true],
    -   Sonata\IntlBundle\SonataIntlBundle::class => ['all' => true],
    -   Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle::class => ['all' => true],
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

## Templates moved

We've moved the following templates:

- `@SyliusAttribute/Types/*.html.twig`  
    You should search for `SyliusAttribute/Types` and `SyliusAttributeBundle:Types` in your templates and make the changes accordingly:
    - in the Admin area: `@SyliusAdmin/Product/Show/Types/*.html.twig`
    - in the Shop area: `@SyliusShop/Product/Show/Types/*.html.twig`

## Billing and shipping addresses have been switched with one another

Until now shipping address used to be the default address of an Order. We have changed that, so now the billing address 
became the default address during checkout. It is an important change in our checkout process, please have that in mind.

⚠️ This change also implies that the Tax calculation is now done on the billing address and not on the shipping address anymore.

## Postgres support

In case when you are using Postgres in your project, function `DATE_FORMAT` should be overridden.
Adjust configuration in `config/packages/doctrine.yaml` to change `DATE_FORMAT` implementation:

```yaml
doctrine:
    orm:
        entity_managers:
            default:
                dql:
                    string_functions:
                        DATE_FORMAT: App\Doctrine\DQL\DateFormat # OR any other path to your implementation
```

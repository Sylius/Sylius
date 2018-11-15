# UPGRADE FROM `v1.3.2` TO `v1.3.3`

* Incorporate [the following changes in your application](https://github.com/Sylius/Sylius/pull/9889/files) in order to 
  be able to override external configuration files like specified in our documentation.

# UPGRADE FROM `v1.2.X` TO `v1.3.0`

* MongoDB and PHPCR drivers have been deprecated in `ResourceBundle` and `GridBundle`.

* Refering to any file from `vendor/sylius/sylius/app` directory has been deprecated.

## Application

* Run `composer config config.platform.php 7.2.4`

* Run `composer require sylius/sylius:~1.3.0 sylius-labs/sensio-distribution-bundle:^6.0 incenteev/composer-parameter-handler:^2.1 --no-update`

* Add the following code in your `behat.yml(.dist)` file:

    ```yaml
    default:
        extensions:
            FriendsOfBehat\SymfonyExtension:
                env_file: ~  
  
    cached:
        extensions:
            FriendsOfBehat\SymfonyExtension:
                env_file: ~
    ```
    
    If you have any of those nodes, don't duplicate them.
    
* Incorporate changes from the following files into your application:

    * [`package.json`](https://github.com/Sylius/Sylius-Standard/blob/1.3/package.json) ([see diff](https://github.com/Sylius/Sylius-Standard/compare/1.2...1.3#diff-e56633f72ecc521128b3db6586074d2c)) 
    * [`.babelrc`](https://github.com/Sylius/Sylius-Standard/blob/1.3/.babelrc) ([see diff](https://github.com/Sylius/Sylius-Standard/compare/1.2...1.3#diff-b9cfc7f2cdf78a7f4b91a753d10865a2))
    * [`.eslintrc.js`](https://github.com/Sylius/Sylius-Standard/blob/1.3/.eslintrc.js) ([see diff](https://github.com/Sylius/Sylius-Standard/compare/1.2...1.3#diff-e4403a877d80de653400d88d85e4801a))
     
* Update PHP and JS dependencies by running `composer update` and `yarn upgrade`

* Clear cache by running `bin/console cache:clear`

* Install assets by `bin/console assets:install web` and `yarn build`

### Optional

#### Travis CI

* Remove the build for PHP 7.1.

#### Behat

* If you want to reuse Sylius contexts, run `composer require friends-of-behat/page-object-extension —-dev` to add a new development dependency.

#### Showing products from descendant taxons

* See [the Github issue](https://github.com/Sylius/Sylius/issues/6604) for the details.

* This feature can be enabled by adding the following config to your application:

    ```yaml
    sylius_shop:
        product_grid:
          include_all_descendants: true
    ```

## Directory structure change

**Sylius 1.3** uses new directory structure, introduced in **Symfony 4**. Even though it's not required,
we strongly encourage you to follow this convention. Take a look at
[official Symfony upgrade documentation](https://symfony.com/doc/current/setup/flex.html#upgrading-existing-applications-to-flex),
to know what exactly should be done.

#### Remember!

After upgrading the catalog structure, copy [public/index.php](https://github.com/Sylius/Sylius-Standard/blob/1.3/public/index.php)
and [bin/console](https://github.com/Sylius/Sylius-Standard/blob/1.3/bin/console) files from **Sylius-Standard**
to make the whole process completed.

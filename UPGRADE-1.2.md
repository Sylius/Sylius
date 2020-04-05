# UPGRADE FROM `v1.2.2` TO `v1.2.3`

* **BC BREAK**: `OrderShowMenuBuilder` constructor now requires the fourth argument being 
  `Symfony\Component\Security\Csrf\CsrfTokenManagerInterface` instance due to security reasons.

# UPGRADE FROM `v1.2.0` TO `v1.2.2`

* **BC BREAK**: `Sylius\Bundle\ResourceBundle\Controller::applyStateMachineTransitionAction` method now includes CSRF token checks due 
  to security reasons. If you used it for REST API, these checks can be disabled by adding 
  `csrf_protection: false` to your routing configuration.

# UPGRADE FROM `v1.1.X` TO `v1.2.0`

## Codebase

* __BC BREAK:__ `Sylius\Bundle\UserBundle\Controller\UserController`'s method `addFlash` has been renamed to
  `addTranslatedFlash` with added scalar typehints for compatibility with both Symfony 3.4 and Symfony 4.0.

* `Sylius\Bundle\CoreBundle\Installer\Requirement\FilesystemRequirements::__construct` deprecates passing
  `string $rootDir` as a second argument, remove it from your calls to be compatible with 2.0 release.

* The deprecated form mapping feature in SonataCoreBundle has been disabled in the app configuration included from SyliusCoreBundle.
  If you depend on the feature in your application, you will need to make the necessary changes. Refer to https://github.com/sonata-project/SonataCoreBundle/pull/462 for more information.

* liip/imagine-bundle has been upgraded to ^2.0, which contains BCs from previous ^1.9.1 version. Please read their upgrade guide https://github.com/liip/LiipImagineBundle/blob/2.0/UPGRADE.md.

* Class `Sylius\Component\Core\Resolver\DefaultShippingMethodResolver` has been deprecated and will be removed in 2.0. `Sylius\Component\Core\Resolver\EligibleDefaultShippingMethodResolver` should be used instead.

## Application

[*See all the changes in `Sylius/Sylius-Standard` here*](https://github.com/Sylius/Sylius-Standard/pull/236/files)

* Open `app/config/routing.yml` and replace

  ```yaml
  _liip_imagine:
      resource: "@LiipImagineBundle/Resources/config/routing.xml"
  ```
    
  with 
  
  ```yaml
  _liip_imagine:
      resource: "@LiipImagineBundle/Resources/config/routing.yaml"
  ```  
    
* Open `app/config/config.yml` and add the following lines at the end of file:

  ```yaml
  liip_imagine:
      resolvers:
          default:
              web_path:
                  web_root: "%kernel.project_dir%/web"
                  cache_prefix: "media/cache"
  ```
  
* Copy file `.babelrc` from Sylius-Standard v1.2.x ([see source here](https://github.com/Sylius/Sylius-Standard/blob/1.2/.babelrc))

* Copy file `.eslintrc.js` from Sylius-Standard v1.2.x ([see source here](https://github.com/Sylius/Sylius-Standard/blob/1.2/.eslintrc.js))

* Copy file `gulpfile.babel.js` from Sylius-Standard v1.2.x ([see source here](https://github.com/Sylius/Sylius-Standard/blob/1.2/gulpfile.babel.js))

* Remove file `Gulpfile.js` (and move your customisations to `gulpfile.babel.js`)

* Synchronise `package.json` with v1.2.0 ([see diff here](https://github.com/Sylius/Sylius-Standard/pull/236/files#diff-b9cfc7f2cdf78a7f4b91a753d10865a2))

* Synchronise `composer.json` with v1.2.0 ([see diff here](https://github.com/Sylius/Sylius-Standard/pull/236/files#diff-b5d0ee8c97c7abd7e3fa29b9a27d1780))

* Run `composer update` and `yarn upgrade`

* Copy file `vendor/sylius/sylius/app/migrations/Version20180226142349.php` to `app/migrations/`

* Clear cache by `bin/console cache:clear`

* Run database migrations by `bin/console doctrine:migrations:migrate` 

* Install assets by `bin/console assets:install web` and `yarn run gulp`

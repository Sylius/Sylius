# UPGRADE FROM `v1.3.X` TO `v1.4.0`


The first step is upgrading Sylius with Composer

- `composer require sylius/sylius:~1.4.0`

### Doctrine migrations

* Change base `AbstractMigration` namespace to `Doctrine\Migrations\AbstractMigration`
* Add `: void` return types to both `up` and `down` functions
* Copy [this](https://github.com/Sylius/Sylius-Standard/blob/1.4/src/Migrations/Version20190109095211.php) and [this](https://github.com/Sylius/Sylius-Standard/blob/1.4/src/Migrations/Version20190109160409.php) migration to your migrations folder or run `doctrine:migrations:diff` to generate new migration with changes from **Sylius**

### Dotenv

* `composer require symfony/dotenv:^4.2 --dev --no-update`
* Follow [Symfony dotenv update guide](https://symfony.com/doc/current/configuration/dot-env-changes.html) to incorporate required changes in `.env` files structure. Optionally, you can take a look at [corresponding PR](https://github.com/Sylius/Sylius-Standard/pull/323) introducing these changes in **Sylius-Standard**

At the apply migrations with `bin/console doctrine:migrations:migrate`.

Don't forget to clear the cache (`bin/console cache:clear`) to be 100% everything is loaded properly.

---

### Behat

If you're using Behat and want to be up-to-date with our configuration, introduce following changes (you can also take a look at [SymfonyExtension UPGRADE file](https://github.com/FriendsOfBehat/SymfonyExtension/blob/master/UPGRADE-2.0.md))
for more details:

* Update required extensions with `composer require friends-of-behat/symfony-extension:^2.0 friends-of-behat/page-object-extension:^0.3 --dev`
* Remove extensions that are not needed with `composer remove friends-of-behat/context-service-extension friends-of-behat/cross-container-extension friends-of-behat/service-container-extension --dev`
* Update your `behat.yml` - look at the diff [here](https://github.com/Sylius/Sylius-Standard/pull/322/files#diff-7bde54db60a6e933518d8b61b929edce)
* Add `FriendsOfBehat\SymfonyExtension\Bundle\FriendsOfBehatSymfonyExtensionBundle::class => ['test' => true, 'test_cached' => true],` to your `bundles.php`
* If you use our Travis CI configuration, follow [these changes](https://github.com/Sylius/Sylius-Standard/pull/322/files#diff-354f30a63fb0907d4ad57269548329e3) introduced in `.travis.yml` file
* Create `config/services_test.yaml` file with the following code and add these your own Behat services as well:
    ```yaml
    imports:
        - { resource: "../vendor/sylius/sylius/src/Sylius/Behat/Resources/config/services.xml" }
    ```
* If you use our Travis CI configuration, create also `config/services_test_cached.yaml` and import the `config/services_test.yaml` file:
    ```yaml
    imports:
        - { resource: "services_test.yaml" }
    ```
* If you use our Travis CI configuration, follow [theses changes](https://github.com/Sylius/Sylius-Standard/pull/323/files#diff-354f30a63fb0907d4ad57269548329e3) introduced in `.travis.yml` file

---

### Deprecations

- Not passing `Sylius\Component\Locale\Context\LocaleContextInterface` instance as the second argument to `Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelper`'s constructor was deprecated

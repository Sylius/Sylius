# Known errors outside of our control

* `Property "code" does not exist in class "Sylius\Component\Currency\Model\CurrencyInterface"` while clearing the cache:

  * Introduced by `symfony/doctrine-bridge v4.3.0`
  * Will be fixed in `symfony/doctrine-bridge v4.3.1` ([see the pull request with fix](https://github.com/symfony/symfony/pull/31749))
  * Could be avoided by adding a conflict with `symfony/doctrine-bridge v4.3.0` to your `composer.json`:
  
    ```json
    {
        "conflict": {
            "symfony/doctrine-bridge": "4.3.0"
        }
    }
    ```
  
* `Argument 1 passed to Sylius\Behat\Context\Api\Admin\ManagingTaxonsContext::__construct() must be an instance of Symfony\Component\HttpKernel\Client, instance of Symfony\Bundle\FrameworkBundle\KernelBrowser given` while running Behat scenarios:

  * Introduced by `symfony/framework-bundle v4.3.0`
  * Will be fixed in `symfony/framework-bundle v4.3.1` ([see the pull request with fix](https://github.com/symfony/symfony/pull/31881))
  * Could be avoided by adding a conflict with `symfony/framework-bundle v4.3.0` to your `composer.json`:
  
    ```json
    {
        "conflict": {
            "symfony/framework-bundle": "4.3.0"
        }
    }
    ```

# UPGRADE FROM `v1.4.X` TO `v1.5.0`

Require upgraded Sylius version using Composer:

```bash
composer require sylius/sylius:~1.5.0
```

Copy [a new migration file](https://raw.githubusercontent.com/Sylius/Sylius-Standard/94888ff604f7dfdcdc7165e82ce0119ce892c17e/src/Migrations/Version20190508083953.php) and run new migrations:

```bash
bin/console doctrine:migrations:migrate
```

# UPGRADE FROM `v1.3.X` TO `v1.4.0`

* Deprecated not passing `Sylius\Component\Locale\Context\LocaleContextInterface` instance as the second argument 
  to `Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelper`'s constructor.

* Due to switch to `doctrine/doctrine-migrations-bundle:^2.0`, some changes in migration files are required:
    * `Doctrine\DBAL\Migrations\AbstractMigration` -> `Doctrine\Migrations\AbstractMigration`
    * `Doctrine\DBAL\Migrations\SkipMigrationException` -> `Doctrine\Migrations\Exception\SkipMigration` (if used)
    * `: void` return type added to both `up` and `down` methods

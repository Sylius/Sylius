# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
references related issues.

- `symfony/framework-bundle:6.2.8`:

  This version is missing the service alias `validator.expression`
  which causes ValidatorException exception to be thrown when using `Expression` constraint. 

- `doctrine/orm:>= 2.16.0`

  This version makes Sylius Fixtures loading fail on the product review fixtures.
  References: https://github.com/doctrine/orm/issues/10869

- `symfony/validator:5.4.25 || 6.2.12 || 6.3.1`

  This version introduced a bug, causing validation constraints to not work.
  References: https://github.com/symfony/symfony/issues/50780

- `stof/doctrine-extensions-bundle:1.8.0`

  This version introduced configuring the metadata cache for the extensions, what breaks the `Timestampable` behaviour.
  This package is not exactly the root of the problem, but it started using a bugged feature of the `gedmo/doctrine-extensions` package.

  References:

    - https://github.com/stof/StofDoctrineExtensionsBundle/issues/455
    - https://github.com/doctrine-extensions/DoctrineExtensions/issues/2600

- `doctrine/doctrine-bundle:2.11.0`:

  This version uses the [readonly property](https://github.com/doctrine/DoctrineBundle/blob/2.11.0/Repository/ServiceEntityRepositoryProxy.php#L34), which is available from PHP 8.1 while the package also supports PHP 7.4 and 8.0, leading to compatibility issues.

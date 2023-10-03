# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
references related issues.

- `lexik/jwt-authentication-bundle: ^2.18`

  After bumping to this version ApiBundle starts failing due to requesting a non-existing `api_platform.openapi.factory.legacy` service.
  As we are not using this service across the ApiBundle we added this conflict to unlock the builds, until we investigate the problem.

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

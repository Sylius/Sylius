# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
references related issues.

- `doctrine/orm:>= 2.16.0`

  This version makes Sylius Fixtures loading fail on the product review fixtures.
  References: https://github.com/doctrine/orm/issues/10869

- `stof/doctrine-extensions-bundle:1.8.0`

  This version introduced configuring the metadata cache for the extensions, what breaks the `Timestampable` behaviour.
  This package is not exactly the root of the problem, but it started using a bugged feature of the `gedmo/doctrine-extensions` package.

  References:

    - https://github.com/stof/StofDoctrineExtensionsBundle/issues/455
    - https://github.com/doctrine-extensions/DoctrineExtensions/issues/2600

- `twig/twig:3.9.0`:

  This version has a bug, which lead to a fatal error:
  `An exception has been thrown during the rendering of a template ("Warning: Undefined variable $blocks").`

# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
references related issues.

- `stof/doctrine-extensions-bundle:1.8.0`

  This version introduced configuring the metadata cache for the extensions, what breaks the `Timestampable` behaviour.
  This package is not exactly the root of the problem, but it started using a bugged feature of the `gedmo/doctrine-extensions` package.

  References:

    - https://github.com/stof/StofDoctrineExtensionsBundle/issues/455
    - https://github.com/doctrine-extensions/DoctrineExtensions/issues/2600

- `gedmo/doctrine-extensions:3.17.0`:

  This version has a bug, which on Symfony 6.4 lead to a fatal error:
  `[Semantical Error] The annotation "@note" in property Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry::$data was never imported.`

# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
references related issues.

- `lexik/jwt-authentication-bundle: ^2.18`

  After bumping to this version ApiBundle starts failing due to requesting a non-existing `api_platform.openapi.factory.legacy` service.
  As we are not using this service across the ApiBundle we added this conflict to unlock the builds, until we investigate the problem.

- `symfony/validator:5.4.25`

  This version introduced a bug, causing validation constraints to not work.
  References: https://github.com/symfony/symfony/issues/50780

- `stof/doctrine-extensions-bundle:1.8.0`

  This version introduced configuring the metadata cache for the extensions, what breaks the `Timestampable` behaviour.
  This package is not exactly the root of the problem, but it started using a bugged feature of the `gedmo/doctrine-extensions` package.

  References:

    - https://github.com/stof/StofDoctrineExtensionsBundle/issues/455
    - https://github.com/doctrine-extensions/DoctrineExtensions/issues/2600

- `api-platform/core:2.7.17`:

  This version introduced class aliases, which lead to a fatal error:
  `The autoloader expected class "ApiPlatform\Core\Bridge\Symfony\Bundle\DependencyInjection\ApiPlatformExtension" to be defined in file ".../vendor/api-platform/core/src/Core/Bridge/Symfony/Bundle/DependencyInjection/ApiPlatformExtension.php". The file was found but the class was not in it, the class name or namespace probably has a typo.`

- `twig/twig:3.9.0`:

  This version has a bug, which lead to a fatal error:
  `An exception has been thrown during the rendering of a template ("Warning: Undefined variable $blocks").`

- `gedmo/doctrine-extensions:3.17.0`:

  This version has a bug, which on Symfony 6.4 leads to a fatal error:
  `[Semantical Error] The annotation "@note" in property Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry::$data was never imported.`

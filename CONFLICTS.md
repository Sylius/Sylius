# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
references related issues.

 - `doctrine/doctrine-bundle:2.3.0`:

   This version makes Gedmo Doctrine Extensions fail (tree and position behaviour mostly).

   References: https://github.com/doctrine/DoctrineBundle/issues/1305

 - `jms/serializer-bundle:4.1.0`:

   This version contains service with a wrong constructor arguments:
   `Invalid definition for service ".container.private.profiler": argument 4 of "JMS\SerializerBundle\Debug\DataCollector::__construct()" accepts "JMS\SerializerBundle\Debug\TraceableDriver", "JMS\SerializerBundle\Debug\TraceableMetadataFactory" passed.`

   References: https://github.com/schmittjoh/JMSSerializerBundle/issues/902
 
 - `symfony/dependency-injection:5.4.5`:
   
   This version is causing a problem with mink session:
  `InvalidArgumentException: Specify session name to get in vendor/friends-of-behat/mink/src/Mink.php:198`,

 - `symfony/framework-bundle:5.4.5`:

   This version is causing a problem with returning null as token from `Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage`
   which leads to wrong solving path prefix by `Sylius\Bundle\ApiBundle\Provider\PathPrefixProvider` in API scenarios

 - `api-platform/core:2.7.0`:

   The FQCN of `ApiPlatform\Core\Metadata\Resource\ResourceNameCollection` has changed to:
   `ApiPlatform\Metadata\Resource\ResourceNameCollection` and due to this fact
   `Sylius\Bundle\ApiBundle\Swagger\AcceptLanguageHeaderDocumentationNormalizer` 
   references this class throws an exception
  `Class "ApiPlatform\Core\Metadata\Resource\ResourceNameCollection" not found`

- `doctrine/migrations:3.5.3`:

  This version is causing a problem with migrations and results in throwing a `Doctrine\Migrations\Exception\MetadataStorageError` exception e.g. when executing `sylius:install` command.
  References: https://github.com/doctrine/migrations/issues/1302

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

- `api-platform/core:2.7.17`:

  This version introduced class aliases, which lead to a fatal error:
  `The autoloader expected class "ApiPlatform\Core\Bridge\Symfony\Bundle\DependencyInjection\ApiPlatformExtension" to be defined in file ".../vendor/api-platform/core/src/Core/Bridge/Symfony/Bundle/DependencyInjection/ApiPlatformExtension.php". The file was found but the class was not in it, the class name or namespace probably has a typo.`

- `twig/twig:3.9.0`:

  This version has a bug, which lead to a fatal error:
  `An exception has been thrown during the rendering of a template ("Warning: Undefined variable $blocks").`

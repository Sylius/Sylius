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
   Psalm error: 
   `UndefinedDocblockClass: Docblock-defined class, interface or enum named UnitEnum does not exist`.

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

- `doctrine/orm:2.15.2`:

  This version introduced a bug, which causes the `ForeignKeyConstraintViolationException` exception to not be thrown when trying to delete a resource with a foreign key constraint.
  References: https://github.com/doctrine/orm/issues/10752

- `doctrine/orm:>=2.15.3`

  This version introduced a bug, causing the bulk editing not to work properly. When deleting two items at once, the second one is deleted and re-added to the database.

- `symfony/validator:5.4.25 || 6.2.12 || 6.3.1`

  This version introduced a bug, causing validation constraints to not work.
  References: https://github.com/symfony/symfony/issues/50780

# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
references related issues.

 - `symfony/cache": "^6.0`, "symfony/amqp-messenger": "^6.0", "symfony/doctrine-messenger": "^6.0", 
"symfony/error-handler": "^6.0", "symfony/redis-messenger": "^6.0", "symfony/stopwatch": "^6.0", "symfony/twig-bridge": "^6.0", 
"symfony/var-dumper": "^6.0", "symfony/var-exporter": "^6.0",:

   These libraries still happen to be installed with Sylius if no flex is used. As we don't support Sf6 yet they are conflicted. Installation of symfony/cache v6.0 results with following error:
   ```
   Uncaught Error: Class "Symfony\Component\Cache\DoctrineProvider" not found
   ```
   
 - `symfony/password-hasher": "^6.0`:

   Symfony in version 5.3 change password hashing logic, and in version 6.0 they removed BC layer
   
   References: https://github.com/Sylius/Sylius/pull/13358

 - `doctrine/doctrine-bundle:2.3.0`:

   This version makes Gedmo Doctrine Extensions fail (tree and position behaviour mostly).

   References: https://github.com/doctrine/DoctrineBundle/issues/1305

 - `jms/serializer-bundle:3.9`:

   This version automatically registered DocBlockDriver, which is always turned on, while docblocks used in our code are not usable with it. Sample error:
   `Can't use incorrect type object for collection in Doctrine\ORM\PersistentCollection:owner`

   References: https://github.com/schmittjoh/JMSSerializerBundle/issues/844

 - `symfony/serializer:4.4.19|5.2.2`:

   These versions of Symfony Serializer introduces a bug with trying to access some private properties that don't have getters.
   
   References: https://github.com/symfony/symfony/pull/40004

 - `symfony/property-info:4.4.22|5.2.7`:

   These versions of Symfony PropertyInfo Component introduce a bug with resolving wrong namespace for some translation entities 
   in Swagger UI docs for API.
   
   The potential solution would be to explicitly define these translation entities as API resources with proper serialization.

   Probably introduced in: https://github.com/symfony/symfony/pull/40811

 - `symfony/dependency-injection:4.4.38|5.4.5`:
   
   These versions are causing a problem with mink session:
  `InvalidArgumentException: Specify session name to get in vendor/friends-of-behat/mink/src/Mink.php:198`,
   Psalm error: 
   `UndefinedDocblockClass: Docblock-defined class, interface or enum named UnitEnum does not exist`.

 - `symfony/framework-bundle:^4.4.38|^5.4.5`:

   These versions are causing a problem with returning null as token from `Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage`
   which leads to wrong solving path prefix by `Sylius\Bundle\ApiBundle\Provider\PathPrefixProvider` in API scenarios

In this section we keep track of the reasons, why some restrictions were added to the `requires` section of `composer.json`

- `doctrine/dbal:^2`:

  In `doctrine/dbal:^3` doctrine column type `json_array` has been removed creating error during a
  doctrine migration

   ```
   Error:  Migration Sylius\Bundle\CoreBundle\Migrations\Version20201130071338
   failed during Execution.
   In Exception.php line 125:
   Unknown column type "json_array" requested. Any Doctrine type that you use   
   has to be registered with \Doctrine\DBAL\Types\Type::addType(). You can get  
   a list of all the known types with \Doctrine\DBAL\Types\Type::getTypesMap(  
   ). If this error occurs during database introspection then you might have f  
   orgotten to register all database types for a Doctrine Type. Use AbstractPl  
   atform#registerDoctrineTypeMapping() or have your custom types implement Ty  
   pe#getMappedDatabaseTypes(). If the type name is empty you might have a pro  
   blem with the cache or forgot some mapping information.
   ```

  References: https://github.com/Sylius/Sylius/issues/13211

- `api-platform/core:2.7.0`:

   The FQCN of `ApiPlatform\Core\Metadata\Resource\ResourceNameCollection` has changed to:
   `ApiPlatform\Metadata\Resource\ResourceNameCollection` and due to this fact
   `Sylius\Bundle\ApiBundle\Swagger\AcceptLanguageHeaderDocumentationNormalizer` 
   references this class throws an exception
  `Class "ApiPlatform\Core\Metadata\Resource\ResourceNameCollection" not found`

- `polishsymfonycommunity/symfony-mocker-container:1.0.6`:

  ```
  PHP Fatal error:  Declaration of PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer::has(string $id): bool 
  must be compatible with Symfony\Component\DependencyInjection\Container::has($id) 
  in /home/runner/work/Sylius/Sylius/vendor/polishsymfonycommunity/symfony-mocker-container/src/DependencyInjection/MockerContainer.php on line 64
  ```

  References: https://github.com/PolishSymfonyCommunity/SymfonyMockerContainer/issues/20

- `doctrine/migrations:3.5.3`:

  This version is causing a problem with migrations and results in throwing a `Doctrine\Migrations\Exception\MetadataStorageError` exception e.g. when executing `sylius:install` command.
  References: https://github.com/doctrine/migrations/issues/1302

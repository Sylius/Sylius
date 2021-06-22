# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
references related issues.

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

 - `symfony/doctrine-bridge:4.4.16`:

   This version of Doctrine Bridge introduces a bug that causes an issue related to `ChannelPricing` mapping.

   References: https://github.com/Sylius/Sylius/issues/11970, https://github.com/symfony/symfony/issues/38861

 - `symfony/property-info:4.4.22|5.2.7`:

   These versions of Symfony PropertyInfo Component introduce a bug with resolving wrong namespace for some translation entities 
   in Swagger UI docs for API.
   
   The potential solution would be to explicitly define these translation entities as API resources with proper serialization.

   Probably introduced in: https://github.com/symfony/symfony/pull/40811

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

 - `api-platform/core:^2.6`:

   API Platform 2.6 introduces a series of issues that make our Behat suite fail.
 
 - `symfony/doctrine-bridge:4.4.16`:

   This version of Doctrine Bridge introduces a bug that causes an issue related to `ChannelPricing` mapping.

   References: https://github.com/Sylius/Sylius/issues/11970, https://github.com/symfony/symfony/issues/38861
   
 - `laminas/laminas-code:^4.0.0`:
 
   Throw many syntax exceptions after running `vendor/bin/psalm --show-info=false` on PHP 7.4:
    
   ```
   Error: Syntax error, unexpected T_STRING, expecting T_PAAMAYIM_NEKUDOTAYIM on line 480
   Error: Syntax error, unexpected T_VARIABLE, expecting ')' on line 480
   Error: Syntax error, unexpected ')' on line 481
   Error: Process completed with exit code 1.
   ```
   
   References: https://github.com/laminas/laminas-code/issues/67

 - `symfony/polyfill-mbstring:1.22.0`:

   `polyfill-mbstring` 1.22.0 causes a problem with static analysis on PHP 7.3. 
   After running `vendor/bin/psalm --show-info=false --php-version=7.3`, the following exception is thrown:

   `ParseError - vendor/symfony/polyfill-mbstring/bootstrap80.php:125:86 - Syntax error, unexpected '=' on line 125 (see https://psalm.dev/173) function mb_scrub(string $string, string $encoding = null): string { $encoding ??= mb_internal_encoding(); return mb_convert_encoding($string, $encoding, $encoding); }`

   References: https://github.com/vimeo/psalm/issues/4961

 - `symfony/property-info:4.4.22|5.2.7`:

   These versions of Symfony PropertyInfo Component introduce a bug with resolving wrong namespace for some translation entities 
   in Swagger UI docs for API.
   
   The potential solution would be to explicitly define these translation entities as API resources with proper serialization.

   Probably introduced in: https://github.com/symfony/symfony/pull/40811

- `doctrine/orm:2.10.0`:

  This version causes a problem with the creation of nested taxons by throwing the exception:
  
  `Gedmo\Exception\UnexpectedValueException: Root cannot be changed manually, change parent instead in vendor/gedmo/doctrine-extensions/src/Tree/Strategy/ORM/Nested.php:145`

  References: https://github.com/doctrine-extensions/DoctrineExtensions/issues/2155

# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
references related issues.

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

   - `doctrine/dbal:^3`:
   
   Doctrine column type `json_array` has been removed creating error during a
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

# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
refereneces related issues.

 - `doctrine/inflector:^1.4`:
    
   Inflector 1.4 changes pluralization of `taxon` from `taxons` (used in Sylius) to `taxa`.
   
   References: https://github.com/doctrine/inflector/issues/147
   
<<<<<<< HEAD
=======
 - `lcobucci/jwt:^3.4`:
 
   Crashes Behat test suite while executing step `And I am logged in as "francis@underwood.com"`
   in the new API context:
    
   ```
   Warning: array_key_exists() expects parameter 2 to be array, null given in vendor/webmozart/assert/src/Assert.php line 1662
   ```
   
>>>>>>> 1.8
 - `symfony/doctrine-bridge:4.4.16`:

   This version of Doctrine Bridge introduces a bug that causes an issue related to `ChannelPricing` mapping.

   References: https://github.com/Sylius/Sylius/issues/11970, https://github.com/symfony/symfony/issues/38861

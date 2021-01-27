# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
refereneces related issues.

 - `api-platform/core:^2.6`:

   API Platform 2.6 introduces a series of issues that make our Behat suite fail.

 - `doctrine/inflector:^1.4`:
    
   Inflector 1.4 changes pluralization of `taxon` from `taxons` (used in Sylius) to `taxa`.
   
   References: https://github.com/doctrine/inflector/issues/147
 
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

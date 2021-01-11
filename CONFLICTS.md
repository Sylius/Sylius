# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
refereneces related issues.

 - `symfony/doctrine-bridge:4.4.16`:

   This version of Doctrine Bridge introduces a bug that causes an issue related to `ChannelPricing` mapping.

   References: https://github.com/Sylius/Sylius/issues/11970, https://github.com/symfony/symfony/issues/38861
   
 - `symfony/polyfill-mbstring": "1.22.0`:
 
   Polyfill-mbstring 1.22.0 has problem with code analyse on php7.3. After run `vendor/bin/psalm --show-info=false --php-version=7.3` throw exception: 
   
   `ParseError - vendor/symfony/polyfill-mbstring/bootstrap80.php:125:86 - Syntax error, unexpected '=' on line 125 (see https://psalm.dev/173) function mb_scrub(string $string, string $encoding = null): string { $encoding ??= mb_internal_encoding(); return mb_convert_encoding($string, $encoding, $encoding); }` 
   
   References: https://github.com/vimeo/psalm/issues/4961

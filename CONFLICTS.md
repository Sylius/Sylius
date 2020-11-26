# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
refereneces related issues.

 - `doctrine/inflector:^1.4`:
    
   Inflector 1.4 changes pluralization of `taxon` from `taxons` (used in Sylius) to `taxa`.
   
   References: https://github.com/doctrine/inflector/issues/147
   
 - `symfony/doctrine-bridge:4.4.16`:

   This version of Doctrine Bridge introduces a bug that causes an issue related to `ChannelPricing` mapping.

   References: https://github.com/Sylius/Sylius/issues/11970, https://github.com/symfony/symfony/issues/38861

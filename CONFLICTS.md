# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
refereneces related issues.

 - `symfony/doctrine-bridge:4.4.16`:

   This version of Doctrine Bridge introduces a bug that causes an issue related to `ChannelPricing` mapping.

   References: https://github.com/Sylius/Sylius/issues/11970, https://github.com/symfony/symfony/issues/38861

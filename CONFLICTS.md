# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
refereneces related issues.

 - `doctrine/inflector:^1.4`:
    
   Inflector 1.4 changes pluralization of `taxon` from `taxons` (used in Sylius) to `taxa`.
   
   References: https://github.com/doctrine/inflector/issues/147
 
 - `symfony/doctrine-bridge:4.4.16`:

   This version of Doctrine Bridge introduces a bug that causes an issue related to `ChannelPricing` mapping.

   References: https://github.com/Sylius/Sylius/issues/11970, https://github.com/symfony/symfony/issues/38861

 - `laminas/laminas-code": "^4.0.0`:
 
   Throw many syntax exceptions after running `vendor/bin/psalm --show-info=false` on `php7.4`
    
   ```
   Error: Syntax error, unexpected T_STRING, expecting T_PAAMAYIM_NEKUDOTAYIM on line 480
   Error: Syntax error, unexpected T_VARIABLE, expecting ')' on line 480
   Error: Syntax error, unexpected ')' on line 481
   Error: Syntax error, unexpected T_STRING, expecting T_PAAMAYIM_NEKUDOTAYIM on line 495
   Error: Syntax error, unexpected T_VARIABLE, expecting ')' on line 495
   Error: Syntax error, unexpected ')' on line 496
   Error: Syntax error, unexpected T_STRING, expecting T_PAAMAYIM_NEKUDOTAYIM on line 59
   Error: Syntax error, unexpected T_VARIABLE, expecting ')' on line 59
   Error: Syntax error, unexpected ',' on line 59
   Error: Syntax error, unexpected ')' on line 63
   Error: Syntax error, unexpected T_STRING, expecting T_PAAMAYIM_NEKUDOTAYIM on line 105
   Error: Syntax error, unexpected T_VARIABLE, expecting ')' on line 105
   Error: Syntax error, unexpected ')' on line 107
   Error: Syntax error, unexpected T_VARIABLE, expecting ')' on line 161
   Error: Syntax error, unexpected ',' on line 161
   Error: Syntax error, unexpected ')' on line 163
   Error: Syntax error, unexpected T_VARIABLE, expecting ')' on line 185
   Error: Syntax error, unexpected ',' on line 185
   Error: Syntax error, unexpected ')' on line 187
   Error: Syntax error, unexpected T_STRING, expecting T_PAAMAYIM_NEKUDOTAYIM on line 161
   Error: Syntax error, unexpected T_VARIABLE, expecting ')' on line 161
   Error: Syntax error, unexpected ')' on line 161
   Error: Process completed with exit code 1.
   ```
   
   References: https://github.com/laminas/laminas-code/issues/67

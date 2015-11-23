CHANGELOG
=========

## v0.16 (2015-xx-xx)

This chapter references the relevant changes done in 0.16 version.

To get the diff between two versions, go to https://github.com/Sylius/Sylius/compare/v0.15.0...master

 * feature #3110 [BC BREAK] Bumped minimal versions, major changes: PHP >=5.5.9, Symfony ^2.7
 * bc break #3364 [BC BREAK] Renamed setDefaultOptions to configureOptions
 * bc break #3536 [BC BREAK] Renamed label to type on adjustment
 * bc break #3610 [BC BREAK] Renamed `sylius_payum.classes.payment_config` to `sylius_payum.classes.gateway_config`

## v0.15 (2015-09-08)

This chapter references the relevant changes done in 0.15 version.

To get the diff between two versions, go to https://github.com/Sylius/Sylius/compare/v0.14.0...v0.15.0

 * bug #3196 [ResourceBundle] Enable max depth check on API serialization
 * feature #3094 [BC BREAK][Locale] Translations based on Intl component, can be enabled or disabled
 * feature #3074 [BC BREAK][Addressing] Countries management - enabling & disabling
 * feature #2999 [BC BREAK][Payment] Remove fluent interfaces from PaymentMethod
 * feature #2887 [BC BREAK][ApiBundle] Change API client public id to be simply random id
 * feature #2717 [BC BREAK] Added SyliusUserBundle together with Customer-User split
 * feature #2752 [BC BREAK] Multi channel support

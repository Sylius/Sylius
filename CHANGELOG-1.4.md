# CHANGELOG FOR `1.4.X`

## v1.4.1 (2019-03-05)

#### TL;DR

- Extracted some packages from Sylius core ([#10182](https://github.com/Sylius/Sylius/issues/10182), [#10184](https://github.com/Sylius/Sylius/issues/10184), [#10188](https://github.com/Sylius/Sylius/issues/10188))

#### Details

- [#10126](https://github.com/Sylius/Sylius/issues/10126) [Docs] Change base dir for override config resources ([@oallain](https://github.com/oallain))
- [#10147](https://github.com/Sylius/Sylius/issues/10147) Remove flush() call, its done in the remover itself ([@stefandoorn](https://github.com/stefandoorn))
- [#10156](https://github.com/Sylius/Sylius/issues/10156) Fix recent Composer deprecations ([@pamil](https://github.com/pamil))
- [#10157](https://github.com/Sylius/Sylius/issues/10157) Update to PHPUnit ^7.0 ([@pamil](https://github.com/pamil))
- [#10162](https://github.com/Sylius/Sylius/issues/10162) Change branches in Sylius PR template to supported ones ([@Zales0123](https://github.com/Zales0123))
- [#10164](https://github.com/Sylius/Sylius/issues/10164) Scaling text input field to keep enough room for the buttons ([@4c0n](https://github.com/4c0n))
- [#10167](https://github.com/Sylius/Sylius/issues/10167) Cart flow documented ([@bartoszpietrzak1994](https://github.com/bartoszpietrzak1994))
- [#10169](https://github.com/Sylius/Sylius/issues/10169) Don't fail on billing or shipping address not set ver.2 ([@DmitriyTrt](https://github.com/DmitriyTrt), [@Zales0123](https://github.com/Zales0123))
- [#10171](https://github.com/Sylius/Sylius/issues/10171) Improve release process docs ([@pamil](https://github.com/pamil))
- [#10175](https://github.com/Sylius/Sylius/issues/10175) [Docs] Reverse parts in Custom Translatable Model ([@xElysioN](https://github.com/xElysioN))
- [#10182](https://github.com/Sylius/Sylius/issues/10182) Extract FixturesBundle ([@pamil](https://github.com/pamil))
- [#10184](https://github.com/Sylius/Sylius/issues/10184) Extract ThemeBundle ([@pamil](https://github.com/pamil))
- [#10185](https://github.com/Sylius/Sylius/issues/10185) Add Sylius demo link ([@kulczy](https://github.com/kulczy))
- [#10186](https://github.com/Sylius/Sylius/issues/10186) Improve shop billing data edit scenario ([@Zales0123](https://github.com/Zales0123))
- [#10188](https://github.com/Sylius/Sylius/issues/10188) Extract Registry component ([@pamil](https://github.com/pamil))

## v1.4.0 (2019-02-04)

#### TL;DR

- PHP 7.3 support ([#9914](https://github.com/Sylius/Sylius/issues/9914))
- Don't miss the v1.4.0-BETA.1's changelog below :tada:

#### Details

- [#9914](https://github.com/Sylius/Sylius/issues/9914) Include PHP 7.3 in the build ([@pamil](https://github.com/pamil))
- [#10112](https://github.com/Sylius/Sylius/issues/10112) [Documentation] Update Sylius config path ([@jelen07](https://github.com/jelen07))
- [#10113](https://github.com/Sylius/Sylius/issues/10113) Require stable FOB/SymfonyExtension v2 ([@pamil](https://github.com/pamil))
- [#10117](https://github.com/Sylius/Sylius/issues/10117) Upgrade guide from `v1.3.X` to `v1.4.0` ([@Zales0123](https://github.com/Zales0123))
- [#10118](https://github.com/Sylius/Sylius/issues/10118) [Product Review] fixed review validation when edited by admin ([@kboduch](https://github.com/kboduch))
- [#10119](https://github.com/Sylius/Sylius/issues/10119) Using channel code in shipping method configuration ([@nedac-sorbo](https://github.com/nedac-sorbo))
- [#10128](https://github.com/Sylius/Sylius/issues/10128) Syntax error in documentation ([@hatem20](https://github.com/hatem20))
- [#10130](https://github.com/Sylius/Sylius/issues/10130) Upgrade guide from v1.2.x to v1.4.0 ([@Zales0123](https://github.com/Zales0123))
- [#10132](https://github.com/Sylius/Sylius/issues/10132) Add missing Length constraint on product translation slug ([@venyii](https://github.com/venyii))
- [#10135](https://github.com/Sylius/Sylius/issues/10135) Move bundle registration from Kernel class to "bundles.php" ([@pamil](https://github.com/pamil))
- [#10136](https://github.com/Sylius/Sylius/issues/10136) [HotFix] 500 on taxons list error fix (, [@Zales0123](https://github.com/Zales0123))
- [#10140](https://github.com/Sylius/Sylius/issues/10140) Use phpspec 5.0 in packages ([@pamil](https://github.com/pamil))
- [#10141](https://github.com/Sylius/Sylius/issues/10141) [1.1] Fix select attributes according to recent Symfony form changes ([@Zales0123](https://github.com/Zales0123))
- [#10145](https://github.com/Sylius/Sylius/issues/10145) Make build passing for TaxonomyBundle ([@pamil](https://github.com/pamil))

## v1.4.0-BETA.1 (2019-01-17)

#### TL;DR

- Switched the default password hashing algorithm to `argon2i` ([#10008](https://github.com/Sylius/Sylius/issues/10008), [#10080](https://github.com/Sylius/Sylius/issues/10080), [#10084](https://github.com/Sylius/Sylius/issues/10084))
- Changed dotenv files handling as according to [Symfony's policy](https://symfony.com/doc/current/configuration/dot-env-changes.html) ([#10089](https://github.com/Sylius/Sylius/issues/10089))
- Upgraded Behat infrastructure to use [FriendsOfBehat\SymfonyExtension v2](https://github.com/FriendsOfBehat/SymfonyExtension) ([#10102](https://github.com/Sylius/Sylius/issues/10102))

#### Details

- [#9677](https://github.com/Sylius/Sylius/issues/9790) Deprecate passing container to ORMTranslatableListener ([@kayneth](https://github.com/kayneth))
- [#9794](https://github.com/Sylius/Sylius/issues/9794) [CoreBundle] First address in the address book should be made default ([@kayneth](https://github.com/kayneth))
- [#9917](https://github.com/Sylius/Sylius/issues/9917) Improve taxon fixtures with translations ([@loic425](https://github.com/loic425))
- [#9962](https://github.com/Sylius/Sylius/issues/9962) Added tax category in shipping method fixture ([@mamazu](https://github.com/mamazu))
- [#9962](https://github.com/Sylius/Sylius/issues/9962) Deprecated not passing shipping category repository to shipping method fixture ([@mamazu](https://github.com/mamazu))
- [#9964](https://github.com/Sylius/Sylius/issues/9964) Making templates deprecated ([@mamazu](https://github.com/mamazu))
- [#9983](https://github.com/Sylius/Sylius/issues/9983) Fix #9899 (main taxon autocomplete drop down now contain full taxon name - with all parents) ([@igormukhingmailcom](https://github.com/igormukhingmailcom))
- [#10008](https://github.com/Sylius/Sylius/issues/10008) ShopUser class is now EncoderAware to provide more flexibility for ch… ()
- [#10046](https://github.com/Sylius/Sylius/issues/10046) Enable strict validation for email ([@fendrychl](https://github.com/fendrychl))
- [#10062](https://github.com/Sylius/Sylius/issues/10062) Make possible to autowire services generated by ResourceBundle ([@pamil](https://github.com/pamil))
- [#10067](https://github.com/Sylius/Sylius/issues/10067) Add support for Symfony 4.2 ([@pamil](https://github.com/pamil))
- [#10079](https://github.com/Sylius/Sylius/issues/10079) [Channel] Shop billing data ([@Zales0123](https://github.com/Zales0123))
- [#10080](https://github.com/Sylius/Sylius/issues/10080) Password hashing - multiple encoders support ([@pamil](https://github.com/pamil))
- [#10084](https://github.com/Sylius/Sylius/issues/10084) Password hashing - update encoder on login ([@pamil](https://github.com/pamil))
- [#10089](https://github.com/Sylius/Sylius/issues/10089) Switch to Symfony's dotenv file handling ([@pamil](https://github.com/pamil))
- [#10090](https://github.com/Sylius/Sylius/issues/10090) Switch to DoctrineMigrationsBundle 2.0 ([@Zales0123](https://github.com/Zales0123))
- [#10091](https://github.com/Sylius/Sylius/issues/10091) Create aliases for named Sylius services ([@pamil](https://github.com/pamil))
- [#10102](https://github.com/Sylius/Sylius/issues/10102) Use FriendsOfBehat\SymfonyExtension v2 ([@pamil](https://github.com/pamil))

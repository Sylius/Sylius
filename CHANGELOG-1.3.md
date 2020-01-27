# CHANGELOG FOR `1.3.X`

## v1.3.16 (2020-01-27)

#### CVE-2020-5218: Ability to switch channels via GET parameter enabled in production environments

*Please refer to [the original security advisory](https://github.com/Sylius/Sylius/security/advisories/GHSA-prg5-hg25-8grq) for the most updated information.*  

**Impact:**

This vulnerability gives the ability to switch channels via the `_channel_code` GET parameter in production environments. This was meant to be enabled only when `%kernel.debug%` is set to true. 

However, if no `sylius_channel.debug` is set explicitly in the configuration, the default value which is `%kernel.debug%` will be not resolved and cast to boolean, enabling this debug feature even if that parameter is set to false.

**Patches:**

Patch has been provided for Sylius 1.3.x and newer - **1.3.16, 1.4.12, 1.5.9, 1.6.5**. Versions older than 1.3 are not covered by our security support anymore.

**Workarounds:**

Unsupported versions could be patched by adding the following configuration to run in production:

```yaml
sylius_channel:
    debug: false
```

## v1.3.14, v1.3.15 (2019-12-03, 2019-12-05)

#### CVE-2019-16768: Internal exception message exposure in login action.

**Details:**

Exception messages from internal exceptions (like database exception) are wrapped by 
`\Symfony\Component\Security\Core\Exception\AuthenticationServiceException` and propagated through the system to UI. 
Therefore, some internal system information may leak and be visible to the customer.

A validation message with the exception details will be presented to the user when one will try to log into the shop.

**Solution:**

This release patches the reported vulnerability. The `src/Sylius/Bundle/UiBundle/Resources/views/Security/_login.html.twig` 
file from Sylius should be overridden and `{{ messages.error(last_error.message) }}` changed to `{{ messages.error(last_error.messageKey) }}`.

## v1.3.13 (2019-05-29)

#### Details

- [#10228](https://github.com/Sylius/Sylius/issues/10228) Improve taxon UI ([@kulczy](https://github.com/kulczy), [@Zales0123](https://github.com/Zales0123))
- [#10290](https://github.com/Sylius/Sylius/issues/10290) [Docs] Update "Customizing Repositories" ([@AdamKasp](https://github.com/AdamKasp))
- [#10299](https://github.com/Sylius/Sylius/issues/10299) [Docs] Update "Customizing Models" ([@Tomanhez](https://github.com/Tomanhez))
- [#10314](https://github.com/Sylius/Sylius/issues/10314) [Docs] Update "Customizing Forms" ([@Tomanhez](https://github.com/Tomanhez))
- [#10315](https://github.com/Sylius/Sylius/issues/10315) [Docs] Update "Customizing Factories" ([@Tomanhez](https://github.com/Tomanhez))
- [#10330](https://github.com/Sylius/Sylius/issues/10330) [Docs] Update "Customizing Controllers" ([@Tomanhez](https://github.com/Tomanhez))
- [#10344](https://github.com/Sylius/Sylius/issues/10344) [Docs] Update "Customizing Templates" ([@Tomanhez](https://github.com/Tomanhez))
- [#10348](https://github.com/Sylius/Sylius/issues/10348) [Docs] Update "customizing menus" ([@AdamKasp](https://github.com/AdamKasp))
- [#10349](https://github.com/Sylius/Sylius/issues/10349) [Docs] Update "Customizing Validation" ([@AdamKasp](https://github.com/AdamKasp))
- [#10351](https://github.com/Sylius/Sylius/issues/10351) [Docs] Update "Customizing translations" ([@AdamKasp](https://github.com/AdamKasp))
- [#10353](https://github.com/Sylius/Sylius/issues/10353) [Docs] Update "Customization flashes " ([@AdamKasp](https://github.com/AdamKasp))
- [#10359](https://github.com/Sylius/Sylius/issues/10359) [Docs] Update "Customizing Grids" ([@Tomanhez](https://github.com/Tomanhez))
- [#10363](https://github.com/Sylius/Sylius/issues/10363) [Behat][Shop] Wait for province form loading ([@Zales0123](https://github.com/Zales0123))
- [#10364](https://github.com/Sylius/Sylius/issues/10364) As an Administrator, I want always to have proper option values selected while editing a product variant ([@Tomanhez](https://github.com/Tomanhez), [@monro93](https://github.com/monro93))
- [#10365](https://github.com/Sylius/Sylius/issues/10365) [Admin][Promotion] Fix removing taxon used in promotion rule ([@GSadee](https://github.com/GSadee))
- [#10372](https://github.com/Sylius/Sylius/issues/10372) Image display in edit form ([@AdamKasp](https://github.com/AdamKasp))
- [#10375](https://github.com/Sylius/Sylius/issues/10375) [Docs] Update "Customizing State Machine" ([@AdamKasp](https://github.com/AdamKasp))
- [#10386](https://github.com/Sylius/Sylius/issues/10386) [Build Fix][Behat] Change scenarios to @javascript due to taxon tree changes ([@Zales0123](https://github.com/Zales0123))
- [#10394](https://github.com/Sylius/Sylius/issues/10394) Fix error caused by the taxon tree ([@kulczy](https://github.com/kulczy))
- [#10407](https://github.com/Sylius/Sylius/issues/10407) Bump the Sylius release versions in docs ([@teohhanhui](https://github.com/teohhanhui))
- [#10414](https://github.com/Sylius/Sylius/issues/10414) Use HTTPS links when possible ([@javiereguiluz](https://github.com/javiereguiluz))

## v1.3.12 (2019-05-07)

#### TL;DR

- Extracted packages from the core ([#10325](https://github.com/Sylius/Sylius/issues/10325), [#10326](https://github.com/Sylius/Sylius/issues/10326), [#10327](https://github.com/Sylius/Sylius/issues/10327))

#### Details

- [#10304](https://github.com/Sylius/Sylius/issues/10304) [Docs] Update contributing guide ([@Tomanhez](https://github.com/Tomanhez))
- [#10308](https://github.com/Sylius/Sylius/issues/10308) Fix base locale ([@igormukhingmailcom](https://github.com/igormukhingmailcom))
- [#10309](https://github.com/Sylius/Sylius/issues/10309) Do not depend on transient dependencies for "symfony/intl" package ([@pamil](https://github.com/pamil))
- [#10320](https://github.com/Sylius/Sylius/issues/10320) fix OrderBundle depends on Core component #10319 ()
- [#10324](https://github.com/Sylius/Sylius/issues/10324) Add a workaround for GridBundle & Symfony 4.2.7 to make tests passing ([@pamil](https://github.com/pamil))
- [#10325](https://github.com/Sylius/Sylius/issues/10325) Extract Mailer component & bundle ([@pamil](https://github.com/pamil))
- [#10326](https://github.com/Sylius/Sylius/issues/10326) [WIP] Extract Grid component & bundle ([@pamil](https://github.com/pamil))
- [#10327](https://github.com/Sylius/Sylius/issues/10327) [WIP] Extract Resource component & bundle ([@pamil](https://github.com/pamil))
- [#10328](https://github.com/Sylius/Sylius/issues/10328) Remove dead configuration related to pre-stable Sylius RBAC ([@pamil](https://github.com/pamil))
- [#10331](https://github.com/Sylius/Sylius/issues/10331) [Shop] Update grid action and filter keys to decouple shop from admin ([@GSadee](https://github.com/GSadee))
- [#10335](https://github.com/Sylius/Sylius/issues/10335) Bring back "pay" grid action for backwards compatibility ([@pamil](https://github.com/pamil))
- [#10338](https://github.com/Sylius/Sylius/issues/10338) Removing unused service ([@loevgaard](https://github.com/loevgaard))
- [#10340](https://github.com/Sylius/Sylius/issues/10340) Fix #9646 by removing lambdas in JS file ([@tchapi](https://github.com/tchapi))
- [#10341](https://github.com/Sylius/Sylius/issues/10341) Revert "Fix base locale" ([@pamil](https://github.com/pamil))
- [#10350](https://github.com/Sylius/Sylius/issues/10350) fix default repository for variant and association type resources ([@loic425](https://github.com/loic425))
- [#10352](https://github.com/Sylius/Sylius/issues/10352) Update documentation products.rst ([@tom-schmitz](https://github.com/tom-schmitz))
- [#10356](https://github.com/Sylius/Sylius/issues/10356) Quick fix product variants api invalid json ([@shql](https://github.com/shql))
- [#10357](https://github.com/Sylius/Sylius/issues/10357) Fix wrong use statement in example ([@teohhanhui](https://github.com/teohhanhui))
- [#10358](https://github.com/Sylius/Sylius/issues/10358) [Maintenance] Upgrade minimal jquery version ([@lchrusciel](https://github.com/lchrusciel))
- [#10360](https://github.com/Sylius/Sylius/issues/10360) Revert "fix default repository for variant and association type resources" ([@lchrusciel](https://github.com/lchrusciel))
- [#10362](https://github.com/Sylius/Sylius/issues/10362) Update release process with dates for 1.5 - 1.7 releases ([@pamil](https://github.com/pamil))

## v1.3.11 (2019-04-15)

#### Details

- [#10178](https://github.com/Sylius/Sylius/issues/10178) Wrong regular expression for locale ([@superbull](https://github.com/superbull))
- [#10279](https://github.com/Sylius/Sylius/issues/10279) [Documentation] [ResourceBundle] 7.1. Overriding the Template and Criteria invalid config ([@kboduch](https://github.com/kboduch))
- [#10283](https://github.com/Sylius/Sylius/issues/10283) [UserBundle] Fix user comparaison on user delete listener ([@loic425](https://github.com/loic425))
- [#10289](https://github.com/Sylius/Sylius/issues/10289) Fix re-authenticating for impersonated users ([@semin-lev](https://github.com/semin-lev), [@lchrusciel](https://github.com/lchrusciel))
- [#10294](https://github.com/Sylius/Sylius/issues/10294) [Docs] Fix presentation of "How to configure mailer" cookbook ([@theyoux](https://github.com/theyoux))
- [#10298](https://github.com/Sylius/Sylius/issues/10298) [DOC] [Installation] Fix minor typo ([@MatthieuCutin](https://github.com/MatthieuCutin))

## v1.3.10 (2019-04-01)

#### Details

- [#9902](https://github.com/Sylius/Sylius/issues/9902) [cs] remove unnecesary variables and if conditions ([@TomasVotruba](https://github.com/TomasVotruba), [@lchrusciel](https://github.com/lchrusciel))
- [#10205](https://github.com/Sylius/Sylius/issues/10205) [Docs] Remove misleading channel context docs ([@Zales0123](https://github.com/Zales0123))
- [#10211](https://github.com/Sylius/Sylius/issues/10211) [Docs] Plugins section update ([@CoderMaggie](https://github.com/CoderMaggie))
- [#10213](https://github.com/Sylius/Sylius/issues/10213) Fix product form submit ([@kulczy](https://github.com/kulczy))
- [#10214](https://github.com/Sylius/Sylius/issues/10214) Add behat/transliterator library ([@mkalkowski83](https://github.com/mkalkowski83))
- [#10215](https://github.com/Sylius/Sylius/issues/10215) Fix Sylius Grid on smaller screens ([@kulczy](https://github.com/kulczy))
- [#10221](https://github.com/Sylius/Sylius/issues/10221) [Docs] Refresh "Installation" section of the book ([@pamil](https://github.com/pamil))
- [#10222](https://github.com/Sylius/Sylius/issues/10222) [Docs] Refresh "Contributing code" section ([@pamil](https://github.com/pamil), [@CoderMaggie](https://github.com/CoderMaggie))
- [#10230](https://github.com/Sylius/Sylius/issues/10230) [Docs] Roadmap Link ([@CoderMaggie](https://github.com/CoderMaggie))
- [#10231](https://github.com/Sylius/Sylius/issues/10231) [Docs] Core Team ([@CoderMaggie](https://github.com/CoderMaggie))
- [#10232](https://github.com/Sylius/Sylius/issues/10232) Make PR template great again ([@Zales0123](https://github.com/Zales0123))
- [#10237](https://github.com/Sylius/Sylius/issues/10237) Fixing incorrect location in documentation for turning off admin norifications ([@officialbalazs](https://github.com/officialbalazs))
- [#10239](https://github.com/Sylius/Sylius/issues/10239) [Resource] [Grid] deprecation warning fixed for deprecated Resource drivers ([@doctorx32](https://github.com/doctorx32))
- [#10242](https://github.com/Sylius/Sylius/issues/10242) Fix variant without options values generation ([@Tomanhez](https://github.com/Tomanhez))
- [#10243](https://github.com/Sylius/Sylius/issues/10243) Taxonomy tree modified - 'go level up' moved to the end of tree ([@AdamKasp](https://github.com/AdamKasp))
- [#10246](https://github.com/Sylius/Sylius/issues/10246) [Phpspec] Add missing specs on customer core model ([@loic425](https://github.com/loic425))
- [#10247](https://github.com/Sylius/Sylius/issues/10247) Non consistent file names ([@AdamKasp](https://github.com/AdamKasp))
- [#10254](https://github.com/Sylius/Sylius/issues/10254) Fix assertion's message for ProductOptionValueCollectionType ([@diimpp](https://github.com/diimpp))
- [#10255](https://github.com/Sylius/Sylius/issues/10255) [HotFix] Conflict with Twig 2.7.3 that breaks themes bundle ([@Zales0123](https://github.com/Zales0123))
- [#10256](https://github.com/Sylius/Sylius/issues/10256) Revert "[HotFix] Conflict with Twig 2.7.3 that breaks themes bundle" ([@pamil](https://github.com/pamil))
- [#10259](https://github.com/Sylius/Sylius/issues/10259) [BuildFix] Ignore psalm annotations ([@Zales0123](https://github.com/Zales0123))
- [#10263](https://github.com/Sylius/Sylius/issues/10263) Fix a grammar mistake ([@romankosiuh](https://github.com/romankosiuh))
- [#10264](https://github.com/Sylius/Sylius/issues/10264) Added a missing word ([@romankosiuh](https://github.com/romankosiuh))
- [#10265](https://github.com/Sylius/Sylius/issues/10265) Add plugin-feature docs style ([@kulczy](https://github.com/kulczy))
- [#10270](https://github.com/Sylius/Sylius/issues/10270) Update installation.rst ([@GCalmels](https://github.com/GCalmels))
- [#10278](https://github.com/Sylius/Sylius/pull/10278) Travis with mySQL 5.7 + product sorting fix ([@Zales0123](https://github.com/Zales0123), [@laSyntez](https://github.com/laSyntez))
- [#10280](https://github.com/Sylius/Sylius/issues/10280) [Travis] Update mysql version to speed up builds ([@Zales0123](https://github.com/Zales0123))

## v1.3.9 (2019-03-05)

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
- [#10188](https://github.com/Sylius/Sylius/issues/10188) Extract Registry component ([@pamil](https://github.com/pamil))

## v1.3.8 (2019-02-04)

#### TL;DR

- PHP 7.3 support ([#9914](https://github.com/Sylius/Sylius/issues/9914))

#### Details

- [#9914](https://github.com/Sylius/Sylius/issues/9914) Include PHP 7.3 in the build ([@pamil](https://github.com/pamil))
- [#10112](https://github.com/Sylius/Sylius/issues/10112) [Documentation] Update Sylius config path ([@jelen07](https://github.com/jelen07))
- [#10118](https://github.com/Sylius/Sylius/issues/10118) [Product Review] fixed review validation when edited by admin ([@kboduch](https://github.com/kboduch))
- [#10119](https://github.com/Sylius/Sylius/issues/10119) Using channel code in shipping method configuration ([@nedac-sorbo](https://github.com/nedac-sorbo))
- [#10128](https://github.com/Sylius/Sylius/issues/10128) Syntax error in documentation ([@hatem20](https://github.com/hatem20))
- [#10132](https://github.com/Sylius/Sylius/issues/10132) Add missing Length constraint on product translation slug ([@venyii](https://github.com/venyii))
- [#10135](https://github.com/Sylius/Sylius/issues/10135) Move bundle registration from Kernel class to "bundles.php" ([@pamil](https://github.com/pamil))
- [#10136](https://github.com/Sylius/Sylius/issues/10136) [HotFix] 500 on taxons list error fix (, [@Zales0123](https://github.com/Zales0123))
- [#10140](https://github.com/Sylius/Sylius/issues/10140) Use phpspec 5.0 in packages ([@pamil](https://github.com/pamil))
- [#10141](https://github.com/Sylius/Sylius/issues/10141) [1.1] Fix select attributes according to recent Symfony form changes ([@Zales0123](https://github.com/Zales0123))
- [#10145](https://github.com/Sylius/Sylius/issues/10145) Make build passing for TaxonomyBundle ([@pamil](https://github.com/pamil))

## v1.3.7 (2019-01-17)

#### TL;DR

- Added support for overriding templates from plugins ([#10082](https://github.com/Sylius/Sylius/issues/10082), [#10083](https://github.com/Sylius/Sylius/issues/10083))
- Fixed pagination on product list page ([#10070](https://github.com/Sylius/Sylius/issues/10070))

#### Details

- [#9988](https://github.com/Sylius/Sylius/issues/9988) Fix when trying to delete shop user having same ID than logged … ([@laurent35240](https://github.com/laurent35240))
- [#10002](https://github.com/Sylius/Sylius/issues/10002) Avoid deprecated notice when using symfony/config > 4.2 ([@odolbeau](https://github.com/odolbeau))
- [#10021](https://github.com/Sylius/Sylius/issues/10021) [Behat] Test for assigning main taxon on new product ([@stefandoorn](https://github.com/stefandoorn), [@Zales0123](https://github.com/Zales0123), [@pamil](https://github.com/pamil))
- [#10026](https://github.com/Sylius/Sylius/issues/10026) External command informing about GUS existence ([@bartoszpietrzak1994](https://github.com/bartoszpietrzak1994))
- [#10039](https://github.com/Sylius/Sylius/issues/10039) Removed unused use statement ([@stefandoorn](https://github.com/stefandoorn))
- [#10040](https://github.com/Sylius/Sylius/issues/10040) [Fixtures] StreetAddress instead of StreetName ([@stefandoorn](https://github.com/stefandoorn))
- [#10043](https://github.com/Sylius/Sylius/issues/10043) Behat JS scenarios war vol.1 ([@Zales0123](https://github.com/Zales0123))
- [#10044](https://github.com/Sylius/Sylius/issues/10044) [Docs] Fix docs with page object extension usage ([@loic425](https://github.com/loic425))
- [#10045](https://github.com/Sylius/Sylius/issues/10045) Add scalar types in Behat/Page/Admin directory ([@Zales0123](https://github.com/Zales0123))
- [#10053](https://github.com/Sylius/Sylius/issues/10053) Fixed sorting path while sorting by position ([@filipcro](https://github.com/filipcro))
- [#10054](https://github.com/Sylius/Sylius/issues/10054) [Admin] Taxon order : fix element(data) always returns 0 ([@pierre-H](https://github.com/pierre-H), [@pamil](https://github.com/pamil))
- [#10059](https://github.com/Sylius/Sylius/issues/10059) Cover specs with PHPStan ([@pamil](https://github.com/pamil))
- [#10061](https://github.com/Sylius/Sylius/issues/10061) GUS existence mentioned in Sylius installation guide ([@bartoszpietrzak1994](https://github.com/bartoszpietrzak1994))
- [#10063](https://github.com/Sylius/Sylius/issues/10063) Do not require clearing cache when changing ResourceBundle drivers or metadata classes ([@pamil](https://github.com/pamil))
- [#10064](https://github.com/Sylius/Sylius/issues/10064) Make Sylius tests not fail on PHP 7.3 ([@Zales0123](https://github.com/Zales0123))
- [#10065](https://github.com/Sylius/Sylius/issues/10065) Remove unused Behat method ([@Zales0123](https://github.com/Zales0123))
- [#10070](https://github.com/Sylius/Sylius/issues/10070) #9699 Fix for viewing products when they belong to a taxon and to one… ([@laurent35240](https://github.com/laurent35240))
- [#10072](https://github.com/Sylius/Sylius/issues/10072) It's 2019! ([@bartoszpietrzak1994](https://github.com/bartoszpietrzak1994))
- [#10076](https://github.com/Sylius/Sylius/issues/10076) [Docs] Remove vagrant references ([@lchrusciel](https://github.com/lchrusciel))
- [#10077](https://github.com/Sylius/Sylius/issues/10077) Fix select attributes according to recent Symfony form changes ([@Zales0123](https://github.com/Zales0123))
- [#10081](https://github.com/Sylius/Sylius/issues/10081) [CoreBundle] Fix Type in Construct for ChannelDeletionListener ([@Donjohn](https://github.com/Donjohn))
- [#10082](https://github.com/Sylius/Sylius/issues/10082) [Theme] Allow overriding templates from plugins (1.2.*) ([@Zales0123](https://github.com/Zales0123), [@pamil](https://github.com/pamil))
- [#10083](https://github.com/Sylius/Sylius/issues/10083) [Theme] Allow overriding templates from plugins (^1.3) ([@Zales0123](https://github.com/Zales0123))
- [#10086](https://github.com/Sylius/Sylius/issues/10086) Remove container cleanup in test environment (1.2) ([@Zales0123](https://github.com/Zales0123))
- [#10088](https://github.com/Sylius/Sylius/issues/10088) Fix GridBundle build ([@Zales0123](https://github.com/Zales0123))
- [#10093](https://github.com/Sylius/Sylius/issues/10093) Typo ([@OskarStark](https://github.com/OskarStark))
- [#10094](https://github.com/Sylius/Sylius/issues/10094) Overriding plugin templates in themes tests ([@Zales0123](https://github.com/Zales0123))
- [#10095](https://github.com/Sylius/Sylius/issues/10095) Fix build failing due to newest twig version ([@Zales0123](https://github.com/Zales0123))
- [#10096](https://github.com/Sylius/Sylius/issues/10096) fix link ([@OskarStark](https://github.com/OskarStark))
- [#10097](https://github.com/Sylius/Sylius/issues/10097) less noise ([@OskarStark](https://github.com/OskarStark))
- [#10100](https://github.com/Sylius/Sylius/issues/10100) [Documentation] Visually mark most of the component&bundle docs outdated ([@kulczy](https://github.com/kulczy), [@CoderMaggie](https://github.com/CoderMaggie))
- [#10101](https://github.com/Sylius/Sylius/issues/10101) Fix build (1.3) ([@Zales0123](https://github.com/Zales0123))

## v1.3.6 (2018-12-17)

#### TL;DR

- Fixed compatibility issues with Symfony 4.1.18 and 4.1.19 ([#10020](https://github.com/Sylius/Sylius/issues/10020), [#10038](https://github.com/Sylius/Sylius/issues/10038))

#### Details

- [#9837](https://github.com/Sylius/Sylius/issues/9837) Repaired shipping method fixture ([@JakobTolkemit](https://github.com/JakobTolkemit))
- [#9893](https://github.com/Sylius/Sylius/issues/9893) Correcting the documentation about how to customise forms templates ([@Konafets](https://github.com/Konafets))
- [#9919](https://github.com/Sylius/Sylius/issues/9919) #9858 Fix for promotion of 100 percent with coupon ([@laurent35240](https://github.com/laurent35240))
- [#9975](https://github.com/Sylius/Sylius/issues/9975) Ignore locale request restriction for profiler and it's toolbar ([@Peteck](https://github.com/Peteck))
- [#9979](https://github.com/Sylius/Sylius/issues/9979) Update book/installation docs with correct config folder ([@dakorpar](https://github.com/dakorpar))
- [#9985](https://github.com/Sylius/Sylius/issues/9985) Add missing code and calculator mandatory field on tax rate documenation ([@Soullivaneuh](https://github.com/Soullivaneuh))
- [#9995](https://github.com/Sylius/Sylius/issues/9995) Remove `AppBundle` from docs. ([@Konafets](https://github.com/Konafets))
- [#9997](https://github.com/Sylius/Sylius/issues/9997) Fix typo cookbook about emails ([@Konafets](https://github.com/Konafets))
- [#9998](https://github.com/Sylius/Sylius/issues/9998) Improve the ShippingBundle doc ([@Konafets](https://github.com/Konafets))
- [#10004](https://github.com/Sylius/Sylius/issues/10004) [Console] Add command for showing available Sylius plugins ([@GSadee](https://github.com/GSadee))
- [#10011](https://github.com/Sylius/Sylius/issues/10011) [Kernel] Move WebServerBundle to dev/test environment ([@GSadee](https://github.com/GSadee))
- [#10012](https://github.com/Sylius/Sylius/issues/10012) Fixed incorrect Behat MinkExtension key in the docs ([@jzawadzki](https://github.com/jzawadzki))
- [#10016](https://github.com/Sylius/Sylius/issues/10016) Column 'position' cannot be null ([@zspine](https://github.com/zspine))
- [#10018](https://github.com/Sylius/Sylius/issues/10018) [docs] fix config directory path and added info for orm mappings in customization/model ([@dakorpar](https://github.com/dakorpar))
- [#10020](https://github.com/Sylius/Sylius/issues/10020) [HotFix][BuildFix] Use old PhpMatcherDumper to avoid trailing slash problems ([@Zales0123](https://github.com/Zales0123))
- [#10023](https://github.com/Sylius/Sylius/issues/10023) Remove billingAddress and shippingAddress ([@Konafets](https://github.com/Konafets))
- [#10025](https://github.com/Sylius/Sylius/issues/10025) [Console] Fix RBAC url ([@GSadee](https://github.com/GSadee))
- [#10029](https://github.com/Sylius/Sylius/issues/10029) Fix type annotation for $addToCartCommand ([@daniellienert](https://github.com/daniellienert))
- [#10038](https://github.com/Sylius/Sylius/issues/10038) Fix the build on 1.3 by more flexible router overriding ([@pamil](https://github.com/pamil))

## v1.3.5 (2018-11-28)

#### TL;DR

- Security fixes according to [problems](https://github.com/dominictarr/event-stream/issues/116) with `dominictarr/event-stream` library
- Hot-fix preventing installation of `symfony/symfony:4.1.8` due to Behat tests problems

#### Details

- [#9860](https://github.com/Sylius/Sylius/issues/9860) [Behat] Viewing errors ([@loic425](https://github.com/loic425))
- [#9932](https://github.com/Sylius/Sylius/issues/9932) [Phpspec] add a missing scenario on customer context spec ([@loic425](https://github.com/loic425))
- [#9934](https://github.com/Sylius/Sylius/issues/9934) Use correct path for view overriding ([@kaszim](https://github.com/kaszim))
- [#9937](https://github.com/Sylius/Sylius/issues/9937) [Payum] Add missing model interfaces ([@GSadee](https://github.com/GSadee))
- [#9945](https://github.com/Sylius/Sylius/issues/9945) Fix for 9942 ([@igormukhingmailcom](https://github.com/igormukhingmailcom))
- [#9949](https://github.com/Sylius/Sylius/issues/9949) Fix sylius:theme:assets:install command ([@alekseyp](https://github.com/alekseyp))
- [#9950](https://github.com/Sylius/Sylius/issues/9950) [Docs][Book] Promotion priorities ([@CoderMaggie](https://github.com/CoderMaggie))
- [#9955](https://github.com/Sylius/Sylius/issues/9955) Remove inline css ([@Prometee](https://github.com/Prometee))
- [#9956](https://github.com/Sylius/Sylius/issues/9956) Update disabling-localised-urls.rst ([@alekseyp](https://github.com/alekseyp))
- [#9961](https://github.com/Sylius/Sylius/issues/9961) Fixed: 9959 (added public/media/image/.gitkeep to repo) ([@igormukhingmailcom](https://github.com/igormukhingmailcom))
- [#9963](https://github.com/Sylius/Sylius/issues/9963) [Docs][OrderBundle] Remove old, incorrect docs ([@CoderMaggie](https://github.com/CoderMaggie))
- [#9970](https://github.com/Sylius/Sylius/issues/9970) [Hot-fix] Make build great again ([@Zales0123](https://github.com/Zales0123))
- [#9971](https://github.com/Sylius/Sylius/issues/9971) Make build great again (one more time) ([@Zales0123](https://github.com/Zales0123))
- [#9972](https://github.com/Sylius/Sylius/issues/9972) Update gulp-livereload ([@kulczy](https://github.com/kulczy))

## v.1.3.4 (2018-11-16)

#### Details

- [#9885](https://github.com/Sylius/Sylius/issues/9885) fixed ad blocking issue ([@loevgaard](https://github.com/loevgaard))
- [#9887](https://github.com/Sylius/Sylius/issues/9887) use behat page object extension ([@loic425](https://github.com/loic425))
- [#9898](https://github.com/Sylius/Sylius/issues/9898) #9862 Number of items in order summary page ([@laurent35240](https://github.com/laurent35240))
- [#9906](https://github.com/Sylius/Sylius/issues/9906) Product images deletion fix ([@Zales0123](https://github.com/Zales0123))
- [#9908](https://github.com/Sylius/Sylius/issues/9908) [Documentation] Add new styles ([@kulczy](https://github.com/kulczy))
- [#9910](https://github.com/Sylius/Sylius/issues/9910) [Composer] Update ApiTestCase ([@lchrusciel](https://github.com/lchrusciel))
- [#9918](https://github.com/Sylius/Sylius/issues/9918) Make use of sylius_core.public_dir in ThemeBundle ([@alekseyp](https://github.com/alekseyp))
- [#9922](https://github.com/Sylius/Sylius/issues/9922) Apply coding standard fixes from SyliusLabs/CodingStandard ^3.0@dev ([@pamil](https://github.com/pamil))
- [#9923](https://github.com/Sylius/Sylius/issues/9923) Use oneline phpdocs for property type info ([@pamil](https://github.com/pamil))
- [#9926](https://github.com/Sylius/Sylius/issues/9926) Fix plugin naming convention documentation ([@Zales0123](https://github.com/Zales0123))
- [#9927](https://github.com/Sylius/Sylius/issues/9927) Fix version widget and add better quality logo ([@kulczy](https://github.com/kulczy))
- [#9929](https://github.com/Sylius/Sylius/issues/9929) Update SyliusLabs/CodingStandard to ^3.0 ([@pamil](https://github.com/pamil))

## v1.3.3 (2018-11-07)

#### TL;DR

- Fixed configuration files overriding in `app/Resources/` ([#9889](https://github.com/Sylius/Sylius/issues/9889))

  **You need to update your application by following [UPGRADE instructions](https://github.com/Sylius/Sylius/blob/1.3/UPGRADE-1.3.md) in order to make use of it.**

#### Details

- [#9836](https://github.com/Sylius/Sylius/issues/9836) [Core] Bad reverting of ShippingPercentageDiscount promotion ([@fendrychl](https://github.com/fendrychl))
- [#9854](https://github.com/Sylius/Sylius/issues/9854) Update installation.rst ([@zghosts](https://github.com/zghosts))
- [#9856](https://github.com/Sylius/Sylius/issues/9856) #9694 Do not show bulk sections and checkboxes if bulk actions are di… ([@laurent35240](https://github.com/laurent35240))
- [#9866](https://github.com/Sylius/Sylius/issues/9866) [Order] Changing function typing ([@Roshyo](https://github.com/Roshyo))
- [#9868](https://github.com/Sylius/Sylius/issues/9868) [Fix] Indentation error .platform.app.yml in docs ([@jatempa](https://github.com/jatempa))
- [#9878](https://github.com/Sylius/Sylius/issues/9878) Fix select attribute values accordion ([@Zales0123](https://github.com/Zales0123))
- [#9883](https://github.com/Sylius/Sylius/issues/9883) Hydrate promotion_rules directly on loading active promotions for a channel (1n) ([@stefandoorn](https://github.com/stefandoorn))
- [#9889](https://github.com/Sylius/Sylius/issues/9889) Allow to overwrite a specific config file ([@pamil](https://github.com/pamil))
- [#9892](https://github.com/Sylius/Sylius/issues/9892) [Order] Removing after SM callback ([@Roshyo](https://github.com/Roshyo))
- [#9900](https://github.com/Sylius/Sylius/issues/9900) Fix typos in BDD Transformers docs ([@sarjon](https://github.com/sarjon))

## v1.3.2 (2018-10-24)

#### Details

- [#9796](https://github.com/Sylius/Sylius/pull/9796) Improve product attributes JS (@Zales0123)
- [#9815](https://github.com/Sylius/Sylius/pull/9815) remove web server bundle on prod environment (@loic425)
- [#9817](https://github.com/Sylius/Sylius/pull/9817) Upgrade security checker (@pamil)
- [#9827](https://github.com/Sylius/Sylius/pull/9827) Custom homepage controller as public service (@davidroberto)
- [#9829](https://github.com/Sylius/Sylius/pull/9829) Wrong usage of returned data (@Prometee)
- [#9830](https://github.com/Sylius/Sylius/pull/9830) SensioGeneratorBundle vs SymfonyMakerBundle (@davidroberto)
- [#9832](https://github.com/Sylius/Sylius/pull/9832) Fix gulp uglify error with arrow functions (@magentix)
- [#9839](https://github.com/Sylius/Sylius/pull/9839) [Docs] How to disable admin notifications (@stefandoorn)
- [#9841](https://github.com/Sylius/Sylius/pull/9841) [Documentation] Make bundle templates extension part correct (@pamil)

## v1.3.1 (2018-10-11)

#### TL;DR

- Fixed templates overriding ([#9726](https://github.com/Sylius/Sylius/pull/9726), [#9804](https://github.com/Sylius/Sylius/pull/9803))

#### Details

- [#8093](https://github.com/Sylius/Sylius/pull/8093) [Order] Fixed sylius:remove-expired-carts help (@sweoggy)
- [#8494](https://github.com/Sylius/Sylius/pull/8494) set gender `u` as default value - resolves #8493 (@pamil, @kochen)
- [#9627](https://github.com/Sylius/Sylius/pull/9627) Narrow down selectors to prevent unexpected bugs (@teohhanhui)
- [#9646](https://github.com/Sylius/Sylius/pull/9646) [Admin][Product edit] Change the value of the taxons individually when checked/unchecked. (@sbarbat)
- [#9685](https://github.com/Sylius/Sylius/pull/9685) Update gulpfile.babel.js (@mihaimitrut)
- [#9726](https://github.com/Sylius/Sylius/pull/9726) Use native Twig references for templates (@wadjeroudi)
- [#9739](https://github.com/Sylius/Sylius/pull/9739) [Documentation] Change parameters to env variables (@Zales0123)
- [#9740](https://github.com/Sylius/Sylius/pull/9740) Change command examples according to new Symfony recommendations (@Zales0123)
- [#9742](https://github.com/Sylius/Sylius/pull/9742) [Behat] Changing my account password with token I received scenario (@loic425)
- [#9743](https://github.com/Sylius/Sylius/pull/9743) Update shipments.rst (@hmonglee)
- [#9746](https://github.com/Sylius/Sylius/pull/9746) [Documentation] v1.3 Update (@CoderMaggie)
- [#9751](https://github.com/Sylius/Sylius/pull/9751) Update PR template (@CoderMaggie)
- [#9752](https://github.com/Sylius/Sylius/pull/9752) Update installation.rst for Flex (@dunglas)
- [#9754](https://github.com/Sylius/Sylius/pull/9754) Fix the "REST APIs" link in the documentation (@dunglas)
- [#9755](https://github.com/Sylius/Sylius/pull/9755) [Documentation] Fix API example for creating a taxon (@pamil)
- [#9756](https://github.com/Sylius/Sylius/pull/9756) Allow for null hostname in ChannelFixture (@pamil)
- [#9757](https://github.com/Sylius/Sylius/pull/9757) Make ArrayGridProvider more performant & suitable for PHP-PM (@pamil)
- [#9758](https://github.com/Sylius/Sylius/pull/9758) [ThemeBundle] Fix risky tests (@pamil)
- [#9759](https://github.com/Sylius/Sylius/pull/9759) [GridBundle] Do not put unnecessary "andWhere" in ExpressionBuilder (@pamil)
- [#9760](https://github.com/Sylius/Sylius/pull/9760) [CoreBundle] Make sure promotion action/rule amount is an integer (@pamil)
- [#9761](https://github.com/Sylius/Sylius/pull/9761) [ThemeBundle] Replace "symfony/symfony" dependency with specific Symfony packages (@pamil)
- [#9762](https://github.com/Sylius/Sylius/pull/9762) [Grid] Fix getting enabled grid items (@pamil)
- [#9763](https://github.com/Sylius/Sylius/pull/9763) Update "Configuring taxation" docs (@pamil)
- [#9764](https://github.com/Sylius/Sylius/pull/9764) [ShippingBundle] Add validation for ShippingMethod calculator (@pamil)
- [#9765](https://github.com/Sylius/Sylius/pull/9765) Keep the existing pagination when changing sorting on product list page (@pamil)
- [#9766](https://github.com/Sylius/Sylius/pull/9766) Update Composer's branch-alias for 1.3 (@pamil)
- [#9769](https://github.com/Sylius/Sylius/pull/9769) [Behat] Add scenarios on resetting password validation feature (@loic425)
- [#9771](https://github.com/Sylius/Sylius/pull/9771) Trigger deprecation when deprecated image fixture definition is used (@pamil)
- [#9772](https://github.com/Sylius/Sylius/pull/9772) Fix doubled province id on checkout addressing page (@pamil)
- [#9774](https://github.com/Sylius/Sylius/pull/9774) Ask for confirmation when cancelling an order (@pamil)
- [#9775](https://github.com/Sylius/Sylius/pull/9775) Limit products shown in associated products autocomplete field (@pamil)
- [#9776](https://github.com/Sylius/Sylius/pull/9776) [Core] Make implicit dependency explicit (@pamil)
- [#9779](https://github.com/Sylius/Sylius/pull/9779) Fix error templates path (@pamil)
- [#9783](https://github.com/Sylius/Sylius/pull/9783) Correct grammar mistake in README (@pamil)
- [#9788](https://github.com/Sylius/Sylius/pull/9788) Update installation.rst (@hmonglee)
- [#9790](https://github.com/Sylius/Sylius/pull/9790) Update disabling-localised-urls.rst (@hmonglee)
- [#9791](https://github.com/Sylius/Sylius/pull/9791) [Docs] Update year in copyright (@CoderMaggie)
- [#9800](https://github.com/Sylius/Sylius/pull/9800) Removed leftover Symfony3 references (@ping-localhost)
- [#9801](https://github.com/Sylius/Sylius/pull/9801) Update template.rst (@bitbager)
- [#9803](https://github.com/Sylius/Sylius/pull/9803) `purge_mode` has been rename to `mode` (@Prometee)
- [#9804](https://github.com/Sylius/Sylius/pull/9804) [ThemeBundle] Add support for Twig namespaced paths and "templates/" top-level directory (@pamil)
- [#9805](https://github.com/Sylius/Sylius/pull/9805) [Shop] Fix password request & contact pages with a mobile view. (@versgui)

## v1.3.0, v1.3.0-BETA (2018-09-27, 2018-09-24)

#### TL;DR

- Bumped minimal PHP version to 7.2 ([#9498](https://github.com/Sylius/Sylius/pull/9498))
- Changed to Symfony 4 directory structure ([#9643](https://github.com/Sylius/Sylius/pull/9643))
- Introduced Symfony Flex support ([#9665](https://github.com/Sylius/Sylius/pull/9665))
- Added possibility of searching products in nested taxons ([#9621](https://github.com/Sylius/Sylius/pull/9621))
- Deprecated MongoDB and PHPCR drivers ([#9551](https://github.com/Sylius/Sylius/pull/9551))
- Started using Rollup to bundle JS code ([#9494](https://github.com/Sylius/Sylius/pull/9494))
- Added support for authorized state in payments ([#9437](https://github.com/Sylius/Sylius/pull/9437))
- Added registration after checkout ([#9656](https://github.com/Sylius/Sylius/pull/9656))
- Fixed promotion rules application ([#9596](https://github.com/Sylius/Sylius/pull/9596))

#### Details

- [#9437](https://github.com/Sylius/Sylius/pull/9437) [Payment] Support for authorized state (@pamil, @JakobTolkemit)
- [#9492](https://github.com/Sylius/Sylius/pull/9492) Update Sylius issue templates (@CoderMaggie)
- [#9494](https://github.com/Sylius/Sylius/pull/9494) Use rollup to bundle JS (ES6 modules) (@teohhanhui)
- [#9498](https://github.com/Sylius/Sylius/pull/9498) Require PHP ^7.2 in Sylius ^1.3 (@pamil)
- [#9551](https://github.com/Sylius/Sylius/pull/9551) Deprecate MongoDB and PHPCR drivers in ResourceBundle and GridBundle (@pamil)
- [#9557](https://github.com/Sylius/Sylius/pull/9557) Use generic names for data-* properties in sylius-lazy-choice-tree.js (@teohhanhui)
- [#9567](https://github.com/Sylius/Sylius/pull/9567) Add a template for security issues (@pamil)
- [#9583](https://github.com/Sylius/Sylius/pull/9583) Remove Symfony Version from README.md (@psren)
- [#9596](https://github.com/Sylius/Sylius/pull/9596) Take unitTotal of order item to check if taxon rule can be applied (@jdeveloper)
- [#9615](https://github.com/Sylius/Sylius/pull/9615) Simplify code of `sylius-product-images-preview` module (@nenadalm)
- [#9616](https://github.com/Sylius/Sylius/pull/9616) Added account verification option to fixture parser (@mamazu)
- [#9621](https://github.com/Sylius/Sylius/pull/9621) Taxon with children taxons behavior in listing (@bartoszpietrzak1994)
- [#9643](https://github.com/Sylius/Sylius/pull/9643) Symfony 4 directory structure (@pamil)
- [#9656](https://github.com/Sylius/Sylius/pull/9656) [Shop] Registration after checkout (@GSadee)
- [#9663](https://github.com/Sylius/Sylius/pull/9663) Theme translation : Add support of Windows OS (@pierre-H)
- [#9665](https://github.com/Sylius/Sylius/pull/9665) Introduce Symfony Flex (@pamil)
- [#9666](https://github.com/Sylius/Sylius/pull/9666) Bring back incenteev/composer-parameter-handler package to keep backwards compatibility better (@pamil)
- [#9671](https://github.com/Sylius/Sylius/pull/9671) Add backwards compatibility layer for Behat configuration referenced in Sylius-Standard (@pamil)
- [#9672](https://github.com/Sylius/Sylius/pull/9672) Provide a BC layer for files in "app/config/" referenced by PluginSkeleton (@pamil)
- [#9676](https://github.com/Sylius/Sylius/pull/9676) Fix routing BC layer (@pamil)
- [#9682](https://github.com/Sylius/Sylius/pull/9682) Remove unused parameters.yml.dist file (@pamil)
- [#9695](https://github.com/Sylius/Sylius/pull/9695) Fix resolving environment variables (@Zales0123)

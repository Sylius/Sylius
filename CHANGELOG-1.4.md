# CHANGELOG FOR `1.4.X`

## v1.4.12 (2020-01-27)

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

## v1.4.10, v1.4.11 (2019-12-03, 2019-12-05)

#### CVE-2019-16768: Internal exception message exposure in login action.

**Details:**

Exception messages from internal exceptions (like database exception) are wrapped by 
`\Symfony\Component\Security\Core\Exception\AuthenticationServiceException` and propagated through the system to UI. 
Therefore, some internal system information may leak and be visible to the customer.

A validation message with the exception details will be presented to the user when one will try to log into the shop.

**Solution:**

This release patches the reported vulnerability. The `src/Sylius/Bundle/UiBundle/Resources/views/Security/_login.html.twig` 
file from Sylius should be overridden and `{{ messages.error(last_error.message) }}` changed to `{{ messages.error(last_error.messageKey) }}`.

## v1.4.9 (2019-10-09)

The last bugfix release for v1.4.x.

#### Details

- [#10641](https://github.com/Sylius/Sylius/issues/10641) [Documentation] Fixtures customization guides - fixes ([@CoderMaggie](https://github.com/CoderMaggie), [@Zales0123](https://github.com/Zales0123))
- [#10645](https://github.com/Sylius/Sylius/issues/10645) [Docs] Fix Blackfire Ad ([@Tomanhez](https://github.com/Tomanhez))
- [#10646](https://github.com/Sylius/Sylius/issues/10646) [Docs] Fix Ad ([@Tomanhez](https://github.com/Tomanhez))
- [#10649](https://github.com/Sylius/Sylius/issues/10649) Update online course ad ([@kulczy](https://github.com/kulczy))
- [#10652](https://github.com/Sylius/Sylius/issues/10652) Add Sylius 1.6 banner to the docs ([@kulczy](https://github.com/kulczy))
- [#10680](https://github.com/Sylius/Sylius/issues/10680) Fix ChannelCollector related serialization issue in Symfony profiler ([@ostrolucky](https://github.com/ostrolucky))
- [#10701](https://github.com/Sylius/Sylius/issues/10701) [Maintenance] Update docs with v1.6 ([@lchrusciel](https://github.com/lchrusciel))
- [#10710](https://github.com/Sylius/Sylius/issues/10710) [Address book] Extensibility improvements ([@cyrosy](https://github.com/cyrosy))
- [#10713](https://github.com/Sylius/Sylius/issues/10713) [Behat] Improve dashboard page extensibility ([@loic425](https://github.com/loic425))
- [#10727](https://github.com/Sylius/Sylius/issues/10727) Fix channels label size and alignment ([@kulczy](https://github.com/kulczy))
- [#10732](https://github.com/Sylius/Sylius/issues/10732) Update course ad ([@kulczy](https://github.com/kulczy))
- [#10739](https://github.com/Sylius/Sylius/issues/10739) [Admin][Adressing] fixed province code validation regex ([@twojtylak](https://github.com/twojtylak))

## v1.4.8 (2019-08-27)

#### Details

- [#10395](https://github.com/Sylius/Sylius/issues/10395) [Docs] How to add your custom fixtures? ([@Tomanhez](https://github.com/Tomanhez))
- [#10397](https://github.com/Sylius/Sylius/issues/10397) [Docs]How to add your custom fixture suites? ([@Tomanhez](https://github.com/Tomanhez))
- [#10512](https://github.com/Sylius/Sylius/issues/10512) [Admin] Improve breadcrumbs (especially for ProductVariants and PromotionCoupons) ([@CoderMaggie](https://github.com/CoderMaggie))
- [#10540](https://github.com/Sylius/Sylius/issues/10540) Skip oauth_user_factory_is_not_overridden test if HWIOAuthBundle is not installed ([@vvasiloi](https://github.com/vvasiloi))
- [#10553](https://github.com/Sylius/Sylius/issues/10553) Flags are not languages ([@vvasiloi](https://github.com/vvasiloi))
- [#10558](https://github.com/Sylius/Sylius/issues/10558) Allow translation of custom labels ([@Prometee](https://github.com/Prometee))
- [#10564](https://github.com/Sylius/Sylius/issues/10564) [Fixture] Improve order fixture ([@Zales0123](https://github.com/Zales0123))
- [#10571](https://github.com/Sylius/Sylius/issues/10571) Update custom-promotion-rule.rst ([@jmwill86](https://github.com/jmwill86))
- [#10579](https://github.com/Sylius/Sylius/issues/10579) Fix lazy choice tree will not automatically expanded ([@tom10271](https://github.com/tom10271))
- [#10583](https://github.com/Sylius/Sylius/issues/10583) Enable sorting of customer orders in admin panel ([@pamil](https://github.com/pamil))
- [#10598](https://github.com/Sylius/Sylius/issues/10598) Add course ad ([@kulczy](https://github.com/kulczy))
- [#10599](https://github.com/Sylius/Sylius/issues/10599) [Documentation] Delete additional lines to remove ShopBundle ([@wpje](https://github.com/wpje))
- [#10601](https://github.com/Sylius/Sylius/issues/10601) Change course CTA ([@kulczy](https://github.com/kulczy))
- [#10603](https://github.com/Sylius/Sylius/issues/10603) [Shop] Promotion integrity checker fix ([@lchrusciel](https://github.com/lchrusciel))
- [#10618](https://github.com/Sylius/Sylius/issues/10618) [Fixtures] Allow no shipping and payments in fixtures ([@igormukhingmailcom](https://github.com/igormukhingmailcom), [@Zales0123](https://github.com/Zales0123))
- [#10624](https://github.com/Sylius/Sylius/issues/10624) Disable chrome autocomplete ([@kulczy](https://github.com/kulczy))
- [#10626](https://github.com/Sylius/Sylius/issues/10626) [Fixture] Do not skip payments and shipments manually ([@Zales0123](https://github.com/Zales0123))
- [#10629](https://github.com/Sylius/Sylius/issues/10629) [Docs] Add missing items to customization guide menu ([@Zales0123](https://github.com/Zales0123))
- [#10633](https://github.com/Sylius/Sylius/issues/10633) Add Blackfire ad ([@kulczy](https://github.com/kulczy))
- [#10634](https://github.com/Sylius/Sylius/issues/10634) Add Blackfire logo ([@kulczy](https://github.com/kulczy))

## v1.4.7 (2019-07-25)

#### Details

- [#10165](https://github.com/Sylius/Sylius/issues/10165) Product attribute fixtures improvements ([@Zales0123](https://github.com/Zales0123), [@pamil](https://github.com/pamil))
- [#10401](https://github.com/Sylius/Sylius/issues/10401) Psalm ([@loic425](https://github.com/loic425), [@pamil](https://github.com/pamil))
- [#10464](https://github.com/Sylius/Sylius/issues/10464) Do not crash when duplicated locales are passed to the fixture ([@pamil](https://github.com/pamil))
- [#10468](https://github.com/Sylius/Sylius/issues/10468) Remove Symfony workarounds and add conflicts ([@pamil](https://github.com/pamil))
- [#10473](https://github.com/Sylius/Sylius/issues/10473) Update docs to follow Symfony 4 standards ([@pamil](https://github.com/pamil))
- [#10488](https://github.com/Sylius/Sylius/issues/10488) Marked router dependency as deprecated in admin ImpersonateUserController ([@SebLours](https://github.com/SebLours))
- [#10489](https://github.com/Sylius/Sylius/issues/10489) Make it possible to have no shipping methods for Order fixtures ([@TiMESPLiNTER](https://github.com/TiMESPLiNTER))
- [#10492](https://github.com/Sylius/Sylius/issues/10492) [Admin] Minor fixes customer group validation form ([@Tomanhez](https://github.com/Tomanhez))
- [#10494](https://github.com/Sylius/Sylius/issues/10494) [UI] Fix button groups radius ([@kulczy](https://github.com/kulczy))
- [#10498](https://github.com/Sylius/Sylius/issues/10498) Add search bar css rule for Firefox ([@aloupfor](https://github.com/aloupfor))
- [#10508](https://github.com/Sylius/Sylius/issues/10508) Revert "Make it possible to have no shipping methods for Order fixtures" ([@lchrusciel](https://github.com/lchrusciel))
- [#10509](https://github.com/Sylius/Sylius/issues/10509) [Admin] Add link to product in variant breadcrumb ([@Tomanhez](https://github.com/Tomanhez))
- [#10517](https://github.com/Sylius/Sylius/issues/10517) [Grid] Allow not to pass "apply_transition" button class ([@Zales0123](https://github.com/Zales0123))
- [#10525](https://github.com/Sylius/Sylius/issues/10525) Bump lodash from 4.17.11 to 4.17.14 ([@dependabot](https://github.com/dependabot)[[@bot](https://github.com/bot)])
- [#10535](https://github.com/Sylius/Sylius/issues/10535) [Shop] Fix passed channel context service to be composite ([@GSadee](https://github.com/GSadee))
- [#10548](https://github.com/Sylius/Sylius/issues/10548) [HotFix?] Move mysql service to fix the build ([@Zales0123](https://github.com/Zales0123))

## v1.4.6 (2019-06-20)

#### Details

- [#10191](https://github.com/Sylius/Sylius/issues/10191) [taxon_fixtures] Fix child taxon slug generation ([@tannyl](https://github.com/tannyl))
- [#10371](https://github.com/Sylius/Sylius/issues/10371) [Docs] How to find out the resource config required when customizing models ([@4c0n](https://github.com/4c0n))
- [#10384](https://github.com/Sylius/Sylius/issues/10384) "Getting Started with Sylius" guide ([@Zales0123](https://github.com/Zales0123), [@CoderMaggie](https://github.com/CoderMaggie))
- [#10389](https://github.com/Sylius/Sylius/issues/10389) [UI] Hide filters by default on index pages ([@Zales0123](https://github.com/Zales0123), [@pamil](https://github.com/pamil))
- [#10404](https://github.com/Sylius/Sylius/issues/10404) Fix huge autocomplete queries issue ([@bitbager](https://github.com/bitbager), [@pamil](https://github.com/pamil))
- [#10412](https://github.com/Sylius/Sylius/issues/10412) [Docs] Added tip for using group sequence validations ([@4c0n](https://github.com/4c0n))
- [#10423](https://github.com/Sylius/Sylius/issues/10423) [Doc] End of bugfix support for 1.3 ([@lchrusciel](https://github.com/lchrusciel))
- [#10426](https://github.com/Sylius/Sylius/issues/10426) Using client from browser kit component instead of http kernel component ([@loevgaard](https://github.com/loevgaard))
- [#10432](https://github.com/Sylius/Sylius/issues/10432) Add known errors section to UPGRADE file ([@pamil](https://github.com/pamil))
- [#10433](https://github.com/Sylius/Sylius/issues/10433) Bump fstream from 1.0.11 to 1.0.12 ([@dependabot](https://github.com/dependabot)[[@bot](https://github.com/bot)])
- [#10440](https://github.com/Sylius/Sylius/issues/10440) Fix removing taxons with numeric codes from products ([@vvasiloi](https://github.com/vvasiloi))
- [#10445](https://github.com/Sylius/Sylius/issues/10445) Fix typos and grammar in the Getting Started guide ([@pamil](https://github.com/pamil))
- [#10446](https://github.com/Sylius/Sylius/issues/10446) Update the 1.1 version status in the release process docs ([@pamil](https://github.com/pamil))
- [#10450](https://github.com/Sylius/Sylius/issues/10450) Fix interfaces mapping in Doctrine for admin user and shop user ([@pamil](https://github.com/pamil))
- [#10462](https://github.com/Sylius/Sylius/issues/10462) [Docs] Update Sylius versions in installation and contribution guides ([@GSadee](https://github.com/GSadee))

## v1.4.5 (2019-05-29)

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
- [#10378](https://github.com/Sylius/Sylius/issues/10378) update documentation how to use api ([@CSchulz](https://github.com/CSchulz))
- [#10386](https://github.com/Sylius/Sylius/issues/10386) [Build Fix][Behat] Change scenarios to @javascript due to taxon tree changes ([@Zales0123](https://github.com/Zales0123))
- [#10394](https://github.com/Sylius/Sylius/issues/10394) Fix error caused by the taxon tree ([@kulczy](https://github.com/kulczy))
- [#10407](https://github.com/Sylius/Sylius/issues/10407) Bump the Sylius release versions in docs ([@teohhanhui](https://github.com/teohhanhui))
- [#10414](https://github.com/Sylius/Sylius/issues/10414) Use HTTPS links when possible ([@javiereguiluz](https://github.com/javiereguiluz))

## v1.4.4 (2019-05-07)

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

## v1.4.3 (2019-04-15)

#### Details

- [#10178](https://github.com/Sylius/Sylius/issues/10178) Wrong regular expression for locale ([@superbull](https://github.com/superbull))
- [#10276](https://github.com/Sylius/Sylius/issues/10276) Upgrade flex to use composer dump-env ([@loic425](https://github.com/loic425))
- [#10279](https://github.com/Sylius/Sylius/issues/10279) [Documentation][ResourceBundle] 7.1. Overriding the Template and Criteria invalid config ([@kboduch](https://github.com/kboduch))
- [#10283](https://github.com/Sylius/Sylius/issues/10283) [UserBundle] Fix user comparaison on user delete listener ([@loic425](https://github.com/loic425))
- [#10289](https://github.com/Sylius/Sylius/issues/10289) Fix re-authenticating for impersonated users ([@semin-lev](https://github.com/semin-lev), [@lchrusciel](https://github.com/lchrusciel))
- [#10294](https://github.com/Sylius/Sylius/issues/10294) [Docs] Fix presentation of "How to configure mailer" cookbook ([@theyoux](https://github.com/theyoux))
- [#10298](https://github.com/Sylius/Sylius/issues/10298) [DOC][Installation] Fix minor typo ([@MatthieuCutin](https://github.com/MatthieuCutin))
- [#10301](https://github.com/Sylius/Sylius/issues/10301) Adopt Symfony 4 directory structure in docs ([@pamil](https://github.com/pamil))

## v1.4.2 (2019-04-01)

#### Details

- [#9902](https://github.com/Sylius/Sylius/issues/9902) [cs] remove unnecesary variables and if conditions ([@TomasVotruba](https://github.com/TomasVotruba), [@lchrusciel](https://github.com/lchrusciel))
- [#10116](https://github.com/Sylius/Sylius/issues/10116) Allow nullable shop billing data ([@Zales0123](https://github.com/Zales0123), [@pamil](https://github.com/pamil))
- [#10197](https://github.com/Sylius/Sylius/issues/10197) [CoreBundle] oauth user provider fix ([@kboduch](https://github.com/kboduch))
- [#10205](https://github.com/Sylius/Sylius/issues/10205) [Docs] Remove misleading channel context docs ([@Zales0123](https://github.com/Zales0123))
- [#10211](https://github.com/Sylius/Sylius/issues/10211) [Docs] Plugins section update ([@CoderMaggie](https://github.com/CoderMaggie))
- [#10213](https://github.com/Sylius/Sylius/issues/10213) Fix product form submit ([@kulczy](https://github.com/kulczy))
- [#10214](https://github.com/Sylius/Sylius/issues/10214) Add behat/transliterator library ([@mkalkowski83](https://github.com/mkalkowski83))
- [#10215](https://github.com/Sylius/Sylius/issues/10215) Fix Sylius Grid on smaller screens ([@kulczy](https://github.com/kulczy))
- [#10220](https://github.com/Sylius/Sylius/issues/10220) [Docs] Refresh the BDD guide ([@pamil](https://github.com/pamil))
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
- [#10278](https://github.com/Sylius/Sylius/issues/10278) Travis with mySQL 5.7 + product sorting fix ([@Zales0123](https://github.com/Zales0123), [@laSyntez](https://github.com/laSyntez))
- [#10280](https://github.com/Sylius/Sylius/issues/10280) [Travis] Update mysql version to speed up builds ([@Zales0123](https://github.com/Zales0123))

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

# CHANGELOG FOR `1.14.X`

## v1.14.14 (2025-12-16)

#### Details

- [#18635](https://github.com/Sylius/Sylius/pull/18635) [RFC] Add telemetry feature 1.14 ([@TheMilek](https://github.com/TheMilek))

## v1.14.13 (2025-11-27)

#### Details

- [#18469](https://github.com/Sylius/Sylius/pull/18469) [CI] Fix Panther tests Chrome user data directory conflict ([@Rafikooo](https://github.com/Rafikooo))
- [#18479](https://github.com/Sylius/Sylius/pull/18479) Add conflict for broken Doctrine ORM version 2.20.7 ([@Rafikooo](https://github.com/Rafikooo))
- [#18549](https://github.com/Sylius/Sylius/pull/18549) Add filtering cve ([@michalkaczmarek-bitbag](https://github.com/michalkaczmarek-bitbag))
- [#18553](https://github.com/Sylius/Sylius/pull/18553) Add md file with description ignored CVE and fix format ([@michalkaczmarek-bitbag](https://github.com/michalkaczmarek-bitbag))
- [#18445](https://github.com/Sylius/Sylius/pull/18445) Validate product's channel eligibility when finalizing an order ([@tomkalon](https://github.com/tomkalon))
- [#18577](https://github.com/Sylius/Sylius/pull/18577) [CS][DX] Refactor
- [#18567](https://github.com/Sylius/Sylius/pull/18567) Fix twice calculation rating review ([@michalkaczmarek-bitbag](https://github.com/michalkaczmarek-bitbag))

## v1.14.12 (2025-10-22)

#### Details

- [#18371](https://github.com/Sylius/Sylius/pull/18371) Update validation group in checkout complete ([@tomkalon](https://github.com/tomkalon))
- [#18380](https://github.com/Sylius/Sylius/pull/18380) Remove product images duplication ([@Glancu](https://github.com/Glancu))
- [#18418](https://github.com/Sylius/Sylius/pull/18418) Revert "[AdminBundle] Hide Impersonate button when user shop account is locked" ([@Rafikooo](https://github.com/Rafikooo))
- [#18399](https://github.com/Sylius/Sylius/pull/18399) Fix invalid property types for 'translations' ([@rust-le](https://github.com/rust-le))
- [#18419](https://github.com/Sylius/Sylius/pull/18419) Bugfix/invalid credentials message is not translated during ajax login ([@michalkaczmarek-bitbag](https://github.com/michalkaczmarek-bitbag))
- [#18411](https://github.com/Sylius/Sylius/pull/18411) Bugfix/wrong assertion in class zone eligible checker ([@michalkaczmarek-bitbag](https://github.com/michalkaczmarek-bitbag))
- [#18406](https://github.com/Sylius/Sylius/pull/18406) Fix: Admin taxon update: Cannot set child as parent to node ([@Glancu](https://github.com/Glancu))
- [#18444](https://github.com/Sylius/Sylius/pull/18444) [CS][DX] Refactor
- [#18459](https://github.com/Sylius/Sylius/pull/18459) Update BuildTestAppAction to v3.0.1 ([@Rafikooo](https://github.com/Rafikooo))

## v1.14.11 (2025-09-25)

#### Details

- [#18364](https://github.com/Sylius/Sylius/pull/18364) [BUGFIX] Correct pricing currency resolution in product variant view ([@tomkalon](https://github.com/tomkalon))
- [#18361](https://github.com/Sylius/Sylius/pull/18361) [DEPRECATIONS] Mark `CartActions` interface and `OrderItemController` as deprecated ([@tomkalon](https://github.com/tomkalon))
- [#18378](https://github.com/Sylius/Sylius/pull/18378) [AdminBundle] Hide Impersonate button when user shop account is locked ([@crydotsnake](https://github.com/crydotsnake))

## v1.14.10 (2025-09-09)

#### Details

- [#18302](https://github.com/Sylius/Sylius/pull/18302) [Composer][1.14] More specific confflict with serializer ([@Jibbarth](https://github.com/Jibbarth))
- [#18303](https://github.com/Sylius/Sylius/pull/18303) [Promotion] Corrects order of amounts to apply correct promotion amount ([@k-kubacki](https://github.com/k-kubacki))
- [#18317](https://github.com/Sylius/Sylius/pull/18317) Adding the isEnabled filter to the countries list in checkout while addressing ([@TheMilek](https://github.com/TheMilek))
- [#18320](https://github.com/Sylius/Sylius/pull/18320) [CS][DX] Refactor
- [#18339](https://github.com/Sylius/Sylius/pull/18339) Fixed placing order with not possible zone ([@pbalcerzak](https://github.com/pbalcerzak))
- [#18345](https://github.com/Sylius/Sylius/pull/18345) Fix argument of ZoneEligibilityChecker to use the new service id ([@GSadee](https://github.com/GSadee))
- [#18351](https://github.com/Sylius/Sylius/pull/18351) [CS][DX] Refactor
- [#17705](https://github.com/Sylius/Sylius/pull/17705) Fix deleting a value from an existing select product attribute while it's in use ([@coldic3](https://github.com/coldic3))

## v1.14.9 (2025-08-14)

#### Details

- [#18293](https://github.com/Sylius/Sylius/pull/18293) Revert "[Fixtures] Make factory example constructor arguments protected" ([@GSadee](https://github.com/GSadee))
- [#18290](https://github.com/Sylius/Sylius/pull/18290) [CS][DX] Refactor
- [#18296](https://github.com/Sylius/Sylius/pull/18296) [Admin][Product] Fix not blank product code constraint message ([@SVillette](https://github.com/SVillette))

## v1.14.8 (2025-07-31)

#### Details

- [#18266](https://github.com/Sylius/Sylius/pull/18266) [Fixtures] Make factory example constructor arguments protected ([@GSadee](https://github.com/GSadee))
- [#18270](https://github.com/Sylius/Sylius/pull/18270) Send Password Request Mail only on enabled Users ([@k-kubacki](https://github.com/k-kubacki))

## v1.14.7 (2025-07-10)

#### Details

- [#18195](https://github.com/Sylius/Sylius/pull/18195) [Composer] Conflict with symfony/serializer:^6.4.23 because of a fatal error in APIP 2.7 ([@GSadee](https://github.com/GSadee))
- [#18199](https://github.com/Sylius/Sylius/pull/18199) [Core] Change where to andWhere method in ProductRepository ([@GSadee](https://github.com/GSadee))

## v1.14.6 (2025-06-17)

#### Details

- [#17952](https://github.com/Sylius/Sylius/pull/17952) Add instruction doc for disabling Mollie on Sylius Standard ([@TheMilek](https://github.com/TheMilek))
- [#17960](https://github.com/Sylius/Sylius/pull/17960) [Admin] Missing role icon in menu ([@PiotrTulacz](https://github.com/PiotrTulacz))
- [#18155](https://github.com/Sylius/Sylius/pull/18155) [ProductBundle] Fix ProductAttributeValue mapping to use ProductAttributeInterface ([@michalsemelka](https://github.com/michalsemelka))

## v1.14.5 (2025-05-07)

#### Details

- [#17838](https://github.com/Sylius/Sylius/issues/17838) [Documentation] Update Contributing Translations page ([@GSadee](https://github.com/GSadee))
- [#17848](https://github.com/Sylius/Sylius/issues/17848) Update validators.de.yml ([@hersche](https://github.com/hersche))
- [#17879](https://github.com/Sylius/Sylius/issues/17879) [Behat] Change driver for statistics and account tests ([@Rafikooo](https://github.com/Rafikooo))
- [#17927](https://github.com/Sylius/Sylius/issues/17927) [CI] Fix failing behat tests ([@mpysiak](https://github.com/mpysiak))
- [#17919](https://github.com/Sylius/Sylius/issues/17919) Fix dev firewall order ([@SVillette](https://github.com/SVillette))
- [#17829](https://github.com/Sylius/Sylius/issues/17829) [Admin] Plus features clickbaits ([@PiotrTulacz](https://github.com/PiotrTulacz))
- [#17937](https://github.com/Sylius/Sylius/issues/17937) [Admin] Plus UTM link ([@PiotrTulacz](https://github.com/PiotrTulacz))
- [#17946](https://github.com/Sylius/Sylius/issues/17946) [Composer] Add conflict to behat/gherkin ([@GSadee](https://github.com/GSadee))

## v1.14.4 (2025-03-31)

#### Details

- [#17709](https://github.com/Sylius/Sylius/issues/17709) Update bunnyshell/workflows to v2 ([@GSadee](https://github.com/GSadee))
- [#17730](https://github.com/Sylius/Sylius/issues/17730) [CI] Update actions/upload-artifact to v4 ([@mpysiak](https://github.com/mpysiak))
- [#17764](https://github.com/Sylius/Sylius/issues/17764) [Behat] Add wait for page reload ([@Wojdylak](https://github.com/Wojdylak))
- [#17767](https://github.com/Sylius/Sylius/issues/17767) RTL for arabic languages 1.14 ([@bartek-sek](https://github.com/bartek-sek))
- [#17770](https://github.com/Sylius/Sylius/issues/17770) [CS][DX] Refactor
- [#17776](https://github.com/Sylius/Sylius/issues/17776) [Bug] Fix missing twig variable ([@mpysiak](https://github.com/mpysiak))
- [#17773](https://github.com/Sylius/Sylius/issues/17773) [Behat] Rename DriverHelper::waitForPageReload to DriverHelper::waitForPageToLoad ([@GSadee](https://github.com/GSadee))
- [#17732](https://github.com/Sylius/Sylius/issues/17732) change github  PR template ([@christopherhero](https://github.com/christopherhero))
- [#17762](https://github.com/Sylius/Sylius/issues/17762) Missing configurationFormType to AsAttributeType ([@tidall87](https://github.com/tidall87))
- [#17790](https://github.com/Sylius/Sylius/issues/17790) [CS][DX] Refactor
- [#17798](https://github.com/Sylius/Sylius/issues/17798) 1.14 rtl fixes ([@bartek-sek](https://github.com/bartek-sek))
- [#17808](https://github.com/Sylius/Sylius/issues/17808) [1.14] Arabic translations ([@CoderMaggie](https://github.com/CoderMaggie))
- [#17792](https://github.com/Sylius/Sylius/issues/17792) Fix config key in upgrade document ([@revoltek-daniel](https://github.com/revoltek-daniel))

## v1.14.3 (2025-02-26)

#### Details

- [#17626](https://github.com/Sylius/Sylius/issues/17626) Update release cycle after 1.12 EOL ([@CoderMaggie](https://github.com/CoderMaggie))

## v1.14.2 (2025-01-13)

#### Details

- [#17550](https://github.com/Sylius/Sylius/issues/17550) [Maintenance][Behat] Enable not creating a driver session in api scenarios ([@NoResponseMate](https://github.com/NoResponseMate))
- [#17554](https://github.com/Sylius/Sylius/issues/17554) [CI] GHA improvements ([@NoResponseMate](https://github.com/NoResponseMate))
- [#17566](https://github.com/Sylius/Sylius/issues/17566) Allow grid action button's link URL to be defined without defining a … ([@JeanDavidDaviet](https://github.com/JeanDavidDaviet))
- [#17598](https://github.com/Sylius/Sylius/issues/17598) [Bug] Fix statistics tests in new year ([@mpysiak](https://github.com/mpysiak))
- [#17590](https://github.com/Sylius/Sylius/issues/17590) Fix Migrations With Custom Table Name ([@Rafikooo](https://github.com/Rafikooo))
- [#17603](https://github.com/Sylius/Sylius/issues/17603) [CS][DX] Refactor
- [#17602](https://github.com/Sylius/Sylius/issues/17602) [CS][DX] Refactor
- [#17611](https://github.com/Sylius/Sylius/issues/17611) [Maintenance][OrderProcessing] Extract adjustment types to be cleared to a parameter ([@NoResponseMate](https://github.com/NoResponseMate))
- [#17607](https://github.com/Sylius/Sylius/issues/17607) [Bug] Fix missing variable ([@mpysiak](https://github.com/mpysiak))
- [#17556](https://github.com/Sylius/Sylius/issues/17556) [Login] remove loader login page in safari when loading page from cache ([@zairigimad](https://github.com/zairigimad))
- [#17613](https://github.com/Sylius/Sylius/issues/17613) [Inventory] Fix availability checker aliases ([@GSadee](https://github.com/GSadee))
- [#17612](https://github.com/Sylius/Sylius/issues/17612) Add help on channel pricing when product is simple ([@ehibes](https://github.com/ehibes))

## v1.14.1 (2024-12-04)

#### Details

- [#17472](https://github.com/Sylius/Sylius/issues/17472) [Docs] Disallow indexing old-docs in search engines ([@CoderMaggie](https://github.com/CoderMaggie))
- [#17471](https://github.com/Sylius/Sylius/issues/17471) Fix channel collector's name to match the tag's id ([@GSadee](https://github.com/GSadee))
- [#17494](https://github.com/Sylius/Sylius/issues/17494) [Docs v1] Organization docs ([@CoderMaggie](https://github.com/CoderMaggie))
- [#17500](https://github.com/Sylius/Sylius/issues/17500) Update README.md ([@kulczy](https://github.com/kulczy))
- [#17502](https://github.com/Sylius/Sylius/issues/17502) Update breadcrumbs.html ([@kulczy](https://github.com/kulczy))
- [#17515](https://github.com/Sylius/Sylius/issues/17515) removing the double "private" in UPGRADE-1.14.md ([@christopherhero](https://github.com/christopherhero))
- [#17525](https://github.com/Sylius/Sylius/issues/17525) [Maintenance] Remove unnecessary definition of interface ([@Wojdylak](https://github.com/Wojdylak))
- [#17527](https://github.com/Sylius/Sylius/issues/17527) Allow to add extra filters on promotion actions ([@lruozzi9](https://github.com/lruozzi9))
- [#17507](https://github.com/Sylius/Sylius/issues/17507) fix: json_decode check always pass ([@sonbui00](https://github.com/sonbui00))
- [#17533](https://github.com/Sylius/Sylius/issues/17533) Tag attribute case mismatch on sylius php attributes ([@ehibes](https://github.com/ehibes))
- [#17537](https://github.com/Sylius/Sylius/issues/17537) [CS][DX] Refactor
- [#17540](https://github.com/Sylius/Sylius/issues/17540) Add a logo used in emails ([@kulczy](https://github.com/kulczy))
- [#17543](https://github.com/Sylius/Sylius/issues/17543) [Behat] Add http accept header to custom item action ([@Wojdylak](https://github.com/Wojdylak))

## v1.14.0 (2024-11-12)

#### Details

- [#17438](https://github.com/Sylius/Sylius/issues/17438) [Upgrade] Refine Sylius 1.14 Upgrade Guide ([@Rafikooo](https://github.com/Rafikooo))

## v1.14.0-RC.1 (2024-11-07)

#### Details

- [#17390](https://github.com/Sylius/Sylius/issues/17390) [Admin] Deprecate NotificationWidgetExtension ([@GSadee](https://github.com/GSadee))
- [#17392](https://github.com/Sylius/Sylius/issues/17392) [Behat] Dehardcode the use of entities ([@GSadee](https://github.com/GSadee))
- [#17401](https://github.com/Sylius/Sylius/issues/17401) [Documentation] Fix 2.0-dev installation instruction tip ([@GSadee](https://github.com/GSadee))
- [#17408](https://github.com/Sylius/Sylius/issues/17408) [API] Use model class parameter instead of hardcoded class for ShippingMethodRule resource ([@GSadee](https://github.com/GSadee))
- [#17417](https://github.com/Sylius/Sylius/issues/17417) [Docs] Add links to 2.x docs on the 1.x documentation ([@CoderMaggie](https://github.com/CoderMaggie))
- [#17380](https://github.com/Sylius/Sylius/issues/17380) [CoreBundle] Fix initial log creation for table sylius_channel_pricing_log_entry ([@jblairy](https://github.com/jblairy))

## v1.14.0-BETA.1 (2024-10-30)

#### Details

- [#17238](https://github.com/Sylius/Sylius/issues/17238) [AdminBundle] Unification os services name - fix ([@Wojdylak](https://github.com/Wojdylak))
- [#17233](https://github.com/Sylius/Sylius/issues/17233) [PaymentBundle] Unification of services names ([@Wojdylak](https://github.com/Wojdylak))
- [#17258](https://github.com/Sylius/Sylius/issues/17258) Fix 1.13 build by temporarily disabling problematic promotion scenarios ([@GSadee](https://github.com/GSadee))
- [#17246](https://github.com/Sylius/Sylius/issues/17246) [UserBundle] Deprecate pin configuration parameter ([@GSadee](https://github.com/GSadee))
- [#17241](https://github.com/Sylius/Sylius/issues/17241) [API] Rename and deprecate route parameters ([@GSadee](https://github.com/GSadee))
- [#17235](https://github.com/Sylius/Sylius/issues/17235) [UserBundle] Deprecate security related classes and services that will be removed in 2.0 ([@GSadee](https://github.com/GSadee))
- [#17242](https://github.com/Sylius/Sylius/issues/17242) [PayumBundle] Unification os services name ([@Wojdylak](https://github.com/Wojdylak))
- [#17260](https://github.com/Sylius/Sylius/issues/17260) [UserBundle] Deprecate pin related services and classes ([@GSadee](https://github.com/GSadee))
- [#17262](https://github.com/Sylius/Sylius/issues/17262) Replace deprecated DOMNodeInserted event with MutationObserver ([@kulczy](https://github.com/kulczy))
- [#17250](https://github.com/Sylius/Sylius/issues/17250) [ApiBundle] Unification of services names ([@Wojdylak](https://github.com/Wojdylak))
- [#17265](https://github.com/Sylius/Sylius/issues/17265) [UserBundle] Fix wrongly deprecated pin related services ([@GSadee](https://github.com/GSadee))
- [#17268](https://github.com/Sylius/Sylius/issues/17268) [CS][DX] Refactor
- [#17244](https://github.com/Sylius/Sylius/issues/17244) [Core] Deprecate sylius_core.autoconfigure_with_attributes configuration parameter ([@GSadee](https://github.com/GSadee))
- [#17269](https://github.com/Sylius/Sylius/issues/17269) [CoreBundle] Unification of services names ([@Wojdylak](https://github.com/Wojdylak))
- [#17275](https://github.com/Sylius/Sylius/issues/17275) [ApiBundle] Unification of services names - fix ([@Wojdylak](https://github.com/Wojdylak))
- [#17273](https://github.com/Sylius/Sylius/issues/17273) [CS][DX] Refactor
- [#17274](https://github.com/Sylius/Sylius/issues/17274) [CoreBundle] Unification of services names - fix ([@Wojdylak](https://github.com/Wojdylak))
- [#17292](https://github.com/Sylius/Sylius/issues/17292) [ShippingBundle] Unification of services names ([@Wojdylak](https://github.com/Wojdylak))
- [#16096](https://github.com/Sylius/Sylius/issues/16096) Adding an index for the `address_log_entries` ([@mamazu](https://github.com/mamazu))
- [#17298](https://github.com/Sylius/Sylius/issues/17298) [CoreBundle] Add missing license file ([@GSadee](https://github.com/GSadee))
- [#17299](https://github.com/Sylius/Sylius/issues/17299) [Docs] Refresh Release Cycle ([@CoderMaggie](https://github.com/CoderMaggie))
- [#17297](https://github.com/Sylius/Sylius/issues/17297) Add info about trademark and logo to bundles and components in 1.14 ([@GSadee](https://github.com/GSadee))
- [#17303](https://github.com/Sylius/Sylius/issues/17303) [CS][DX] Refactor
- [#17306](https://github.com/Sylius/Sylius/issues/17306) [All] Unification of services names ([@Wojdylak](https://github.com/Wojdylak))
- [#16601](https://github.com/Sylius/Sylius/issues/16601) [ApiBundle][Image] Prevent resolving non-serialized image paths ([@Wojdylak](https://github.com/Wojdylak))
- [#17313](https://github.com/Sylius/Sylius/issues/17313) [ApiBundle] Deprecate PaymentMethodFilter and ShippingMethodFilter classes ([@Wojdylak](https://github.com/Wojdylak))
- [#17321](https://github.com/Sylius/Sylius/issues/17321) [API][PHPSpec] Remove unused arguments from ImageNormalizerSpec ([@GSadee](https://github.com/GSadee))
- [#17318](https://github.com/Sylius/Sylius/issues/17318) [API] Deprecate legacy_error_handling and serialization_groups configuration parameters ([@GSadee](https://github.com/GSadee))
- [#17320](https://github.com/Sylius/Sylius/issues/17320) Deprecate exceptions from ApiBundle ([@TheMilek](https://github.com/TheMilek))
- [#17281](https://github.com/Sylius/Sylius/issues/17281) chore(doc): add naming changes for plugin skeleton ([@ebuildy](https://github.com/ebuildy))
- [#17342](https://github.com/Sylius/Sylius/issues/17342) [Admin] Deprecate NotificationController ([@GSadee](https://github.com/GSadee))
- [#17337](https://github.com/Sylius/Sylius/issues/17337) [AdminBundle] Add base form type for ShopUserType and  PromotionCouponGeneratorInstructionType ([@Wojdylak](https://github.com/Wojdylak))
- [#17353](https://github.com/Sylius/Sylius/issues/17353) Fix OrderAdjustmentsRecalculationTest + apply API statistics constraints sequentially ([@GSadee](https://github.com/GSadee))
- [#17256](https://github.com/Sylius/Sylius/issues/17256) [API][DOC]Adding a tip about api's preventing methods beside GET to load non cart orders by default ([@oliverde8](https://github.com/oliverde8))
- [#17354](https://github.com/Sylius/Sylius/issues/17354) [API] Refactor statistics constraints ([@GSadee](https://github.com/GSadee))
- [#17363](https://github.com/Sylius/Sylius/issues/17363) [Admin] Pass admin path name parameter to AdminUriBasedSectionResolver ([@GSadee](https://github.com/GSadee))
- [#17356](https://github.com/Sylius/Sylius/issues/17356) [Core][Promotion] Add missing type to AsCatalogPromotionPriceCalculator attribute + add missing AsCatalogPromotionVariantChecker attribute ([@GSadee](https://github.com/GSadee))
- [#17360](https://github.com/Sylius/Sylius/issues/17360) [Maintenance] Remove enshrined/svg-sanitize package ([@mpysiak](https://github.com/mpysiak))
- [#17374](https://github.com/Sylius/Sylius/issues/17374) [CS][DX] Refactor
- [#17373](https://github.com/Sylius/Sylius/issues/17373) [Addressing][Product] Deprecate controllers ([@GSadee](https://github.com/GSadee))

## v1.14.0-ALPHA.2 (2024-10-15)

#### Details

- [#17012](https://github.com/Sylius/Sylius/issues/17012) [CI] Remove unused social media notifications action +  improvements ([@GSadee](https://github.com/GSadee))
- [#17017](https://github.com/Sylius/Sylius/issues/17017) [API] Add missing endpoint for ShopUser in shop context ([@GSadee](https://github.com/GSadee))
- [#17044](https://github.com/Sylius/Sylius/issues/17044) [Docs] Add 2.0-dev installation instructions ([@CoderMaggie](https://github.com/CoderMaggie))
- [#17061](https://github.com/Sylius/Sylius/issues/17061) Deprecate templating helpers ([@GSadee](https://github.com/GSadee))
- [#17073](https://github.com/Sylius/Sylius/issues/17073) [Maintenance] Fix packages in definitions of deprecated services + add missing notes ([@GSadee](https://github.com/GSadee))
- [#17076](https://github.com/Sylius/Sylius/issues/17076) Deprecate ResourceDeleteSubcriber ([@TheMilek](https://github.com/TheMilek))
- [#17088](https://github.com/Sylius/Sylius/issues/17088) Fix name of argument in deprecation trigger for PriceExtension ([@GSadee](https://github.com/GSadee))
- [#17092](https://github.com/Sylius/Sylius/issues/17092) [CS][DX] Refactor
- [#17096](https://github.com/Sylius/Sylius/issues/17096) [AttributeBundle] Unification of service names ([@Wojdylak](https://github.com/Wojdylak))
- [#17095](https://github.com/Sylius/Sylius/issues/17095) [AddressingBundle] Unification of service names ([@Wojdylak](https://github.com/Wojdylak))
- [#17098](https://github.com/Sylius/Sylius/issues/17098) [ChannelBundle] Unification of service names ([@Wojdylak](https://github.com/Wojdylak))
- [#17070](https://github.com/Sylius/Sylius/issues/17070) Fix comparison of order items ([@jaroslavtyc](https://github.com/jaroslavtyc))
- [#17099](https://github.com/Sylius/Sylius/issues/17099) [CurrencyBundle] Unification of service names ([@Wojdylak](https://github.com/Wojdylak))
- [#17103](https://github.com/Sylius/Sylius/issues/17103) [InventoryBundle] Unification of service names ([@Wojdylak](https://github.com/Wojdylak))
- [#17105](https://github.com/Sylius/Sylius/issues/17105) [MoneyBundle] Unification of service names ([@Wojdylak](https://github.com/Wojdylak))
- [#17104](https://github.com/Sylius/Sylius/issues/17104) [LocaleBundle] Unification of service names ([@Wojdylak](https://github.com/Wojdylak))
- [#17120](https://github.com/Sylius/Sylius/issues/17120) Add gedmo/doctrine-extensions conflict ([@mpysiak](https://github.com/mpysiak))
- [#17111](https://github.com/Sylius/Sylius/issues/17111) [OrderBundle] Unification of service names ([@Wojdylak](https://github.com/Wojdylak))
- [#17125](https://github.com/Sylius/Sylius/issues/17125) [LocaleBundle] Unification of service names - fix ([@Wojdylak](https://github.com/Wojdylak))
- [#17138](https://github.com/Sylius/Sylius/issues/17138) Remove winzouStateMachineBundle from packages' test applications to support ResourceBundle 1.12 ([@GSadee](https://github.com/GSadee))
- [#17112](https://github.com/Sylius/Sylius/issues/17112) [ProductBundle] Unification of service names ([@Wojdylak](https://github.com/Wojdylak))
- [#17144](https://github.com/Sylius/Sylius/issues/17144) [Docs] Fix readthedocs configuration file ([@CoderMaggie](https://github.com/CoderMaggie))
- [#17140](https://github.com/Sylius/Sylius/issues/17140) [Maintenance] Resolve gedmo/doctrine-extension conflict ([@mpysiak](https://github.com/mpysiak))
- [#17133](https://github.com/Sylius/Sylius/issues/17133) [PromotionBundle] Unification of service names ([@Wojdylak](https://github.com/Wojdylak))
- [#17148](https://github.com/Sylius/Sylius/issues/17148) [CI] Remove currently unneeded custom build ([@GSadee](https://github.com/GSadee))
- [#17139](https://github.com/Sylius/Sylius/issues/17139) [Composer] Remove conflict to doctrine/orm ([@GSadee](https://github.com/GSadee))
- [#17152](https://github.com/Sylius/Sylius/issues/17152) [PromotionBundle] Unification of service names - fix ([@Wojdylak](https://github.com/Wojdylak))
- [#17093](https://github.com/Sylius/Sylius/issues/17093) Create the "Services naming convention" ADR ([@Wojdylak](https://github.com/Wojdylak))
- [#17163](https://github.com/Sylius/Sylius/issues/17163) Add the packages that have become optional in version 1.12 of the ResourceBundle. ([@Wojdylak](https://github.com/Wojdylak))
- [#17169](https://github.com/Sylius/Sylius/issues/17169) Update README.md ([@kulczy](https://github.com/kulczy))
- [#17162](https://github.com/Sylius/Sylius/issues/17162) Fix session unavailable in locale context ([@NoResponseMate](https://github.com/NoResponseMate))
- [#17170](https://github.com/Sylius/Sylius/issues/17170) [TaxationBundle] Unification of service names ([@Wojdylak](https://github.com/Wojdylak))
- [#17168](https://github.com/Sylius/Sylius/issues/17168) Depreciate PercentageDiscountActionConfigurationType and move to CoreBundle ([@Wojdylak](https://github.com/Wojdylak))
- [#17157](https://github.com/Sylius/Sylius/issues/17157) [ReviewBundle] Unification of service names ([@Wojdylak](https://github.com/Wojdylak))
- [#17167](https://github.com/Sylius/Sylius/issues/17167) [ShippingBundle] Unification of service names ([@Wojdylak](https://github.com/Wojdylak))
- [#17186](https://github.com/Sylius/Sylius/issues/17186) [TaxonomyBundle] Unification of service names ([@Wojdylak](https://github.com/Wojdylak))
- [#17187](https://github.com/Sylius/Sylius/issues/17187) [UserBundle] Unification of service names ([@Wojdylak](https://github.com/Wojdylak))
- [#17181](https://github.com/Sylius/Sylius/issues/17181) [UiBundle] Unification of service names and add deprecations ([@Wojdylak](https://github.com/Wojdylak))
- [#17202](https://github.com/Sylius/Sylius/issues/17202) [PromotionBundle] Unification of sylius.registry_promotion_action ([@Wojdylak](https://github.com/Wojdylak))
- [#17212](https://github.com/Sylius/Sylius/issues/17212) [UiBundle] Deprecate classes and services moved to TwigExtra package ([@GSadee](https://github.com/GSadee))
- [#17216](https://github.com/Sylius/Sylius/issues/17216) [CS][DX] Refactor
- [#17189](https://github.com/Sylius/Sylius/issues/17189) [ShopBundle] Unification of service names ([@Wojdylak](https://github.com/Wojdylak))
- [#17219](https://github.com/Sylius/Sylius/issues/17219) [Behat] Normalize whitespace characters in string ([@Wojdylak](https://github.com/Wojdylak))
- [#17223](https://github.com/Sylius/Sylius/issues/17223) [Core] Deprecate sylius_core.state_machine configuration parameter ([@GSadee](https://github.com/GSadee))
- [#17199](https://github.com/Sylius/Sylius/issues/17199) [AdminBundle] Unification of service names ([@Wojdylak](https://github.com/Wojdylak))
- [#17228](https://github.com/Sylius/Sylius/issues/17228) [ShopBundle] Unification of service name - fix ([@Wojdylak](https://github.com/Wojdylak))
- [#17215](https://github.com/Sylius/Sylius/issues/17215) [UiBundle] Deprecate FilterStorage and FilterStorageInterface classes ([@Wojdylak](https://github.com/Wojdylak))
- [#17226](https://github.com/Sylius/Sylius/issues/17226) [Upgrade] Fix Typo ([@Rafikooo](https://github.com/Rafikooo))
- [#17180](https://github.com/Sylius/Sylius/issues/17180) [Maintenance] Update exception catching when storage is unavailable while getting current locale ([@NoResponseMate](https://github.com/NoResponseMate))

## v1.14.0-ALPHA.1 (2024-09-24)

#### Details

- [#16184](https://github.com/Sylius/Sylius/issues/16184) Update phpstan/phpstan-doctrine requirement from 1.3.43 to 1.3.69 ([@app/dependabot](https://github.com/app/dependabot))
- [#16242](https://github.com/Sylius/Sylius/issues/16242) [Maintenance] Adjust Sylius version on 1.14 branch ([@GSadee](https://github.com/GSadee))
- [#16261](https://github.com/Sylius/Sylius/issues/16261) [Admin] Deprecate FormTypeExtensions ([@TheMilek](https://github.com/TheMilek))
- [#16253](https://github.com/Sylius/Sylius/issues/16253) [Core] Add deprecation to StateMachineExtension ([@Wojdylak](https://github.com/Wojdylak))
- [#16262](https://github.com/Sylius/Sylius/issues/16262) [ADR] Use Separate Base Form Types Instead of Type Extensions ([@TheMilek](https://github.com/TheMilek))
- [#16276](https://github.com/Sylius/Sylius/issues/16276) [Admin] Add base form type for every resource used in AdminBundle ([@TheMilek](https://github.com/TheMilek))
- [#16286](https://github.com/Sylius/Sylius/issues/16286) [Core] Add ShowAvailablePluginsCommand deprecation ([@mpysiak](https://github.com/mpysiak))
- [#16289](https://github.com/Sylius/Sylius/issues/16289) Add missing ShipmentShipType to AdminBundle ([@TheMilek](https://github.com/TheMilek))
- [#16333](https://github.com/Sylius/Sylius/issues/16333) [Inventory] Deprecate extending \InvalidArgumentException by inventory exceptions ([@GSadee](https://github.com/GSadee))
- [#16465](https://github.com/Sylius/Sylius/issues/16465) Deprecate CustomerTypeExtension and AddUserFormSubscriber ([@TheMilek](https://github.com/TheMilek))
- [#16496](https://github.com/Sylius/Sylius/issues/16496) Add upmerge from 2.0 to symfony-7 branch ([@loic425](https://github.com/loic425))
- [#16510](https://github.com/Sylius/Sylius/issues/16510) Deprecate statistics services ([@mpysiak](https://github.com/mpysiak))
- [#16539](https://github.com/Sylius/Sylius/issues/16539) Deprecate LocaleTypeExtension ([@Wojdylak](https://github.com/Wojdylak))
- [#16612](https://github.com/Sylius/Sylius/issues/16612) [CI] Run 1.14 full build instead of unsupported 1.12 ([@GSadee](https://github.com/GSadee))
- [#16647](https://github.com/Sylius/Sylius/issues/16647) Deprecate PromotionCouponPromotionFilter ([@TheMilek](https://github.com/TheMilek))
- [#16703](https://github.com/Sylius/Sylius/issues/16703) [Maintenance] Less referrer usage in static redirects ([@NoResponseMate](https://github.com/NoResponseMate), [@GSadee](https://github.com/GSadee))
- [#16715](https://github.com/Sylius/Sylius/issues/16715) [Admin] Fix resending order confirmation emails ([@Wojdylak](https://github.com/Wojdylak))
- [#16769](https://github.com/Sylius/Sylius/issues/16769) [Money] Deprecate unneeded templating helpers ([@GSadee](https://github.com/GSadee))
- [#16825](https://github.com/Sylius/Sylius/issues/16825) [CI] Update Sylius branches in  workflow ([@GSadee](https://github.com/GSadee))
- [#16837](https://github.com/Sylius/Sylius/issues/16837) [CS][DX] Refactor
- [#16852](https://github.com/Sylius/Sylius/issues/16852) Remove api-platform-3 and add payment-request to upmerge workflow ([@GSadee](https://github.com/GSadee))
- [#16817](https://github.com/Sylius/Sylius/issues/16817) [Maintenance] Bump Sylius resource packages to ^1.11 ([@NoResponseMate](https://github.com/NoResponseMate))
- [#16884](https://github.com/Sylius/Sylius/issues/16884) [CS][DX] Refactor
- [#16729](https://github.com/Sylius/Sylius/issues/16729) [CI] Upmerge Action False Positive ([@Rafikooo](https://github.com/Rafikooo))
- [#16920](https://github.com/Sylius/Sylius/issues/16920) Revert "[CI] Upmerge Action False Positive" ([@Rafikooo](https://github.com/Rafikooo))
- [#16947](https://github.com/Sylius/Sylius/issues/16947) [Admin] Deprecate admin routes ([@mpysiak](https://github.com/mpysiak))
- [#16954](https://github.com/Sylius/Sylius/issues/16954) [Shop] Deprecate unused routes ([@mpysiak](https://github.com/mpysiak))
- [#16956](https://github.com/Sylius/Sylius/issues/16956) [Maintenance] Deprecate leftover legacy custom promotion validation ([@NoResponseMate](https://github.com/NoResponseMate))
- [#16958](https://github.com/Sylius/Sylius/issues/16958) [Maintenance] Remove `bootstrap-shop` from upmerges ([@NoResponseMate](https://github.com/NoResponseMate))
- [#16886](https://github.com/Sylius/Sylius/issues/16886) make it possible to resend order confirmation after fulfilled state ([@zairigimad](https://github.com/zairigimad))

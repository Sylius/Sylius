# CHANGELOG FOR `2.2.X`

## v2.2.8 (2026-07-31)

#### Details

- [#19154](https://github.com/Sylius/Sylius/issues/19154) [FIX][2.2] Hide notifications icon when notifications are disabled ([@jkindly](https://github.com/jkindly))
- [#18930](https://github.com/Sylius/Sylius/issues/18930) [BUGFIX] Add validator to restrict payment requests to placed orders only ([@rust-le](https://github.com/rust-le))
- [#19159](https://github.com/Sylius/Sylius/issues/19159) [API] Refactor payment request eligibility validator and mark new services as experimental ([@GSadee](https://github.com/GSadee))
- [#19161](https://github.com/Sylius/Sylius/issues/19161) [AdminBundle][ShopBundle] Fix production asset build broken by Sass BOM ([@bartek-sek](https://github.com/bartek-sek))
- [#19156](https://github.com/Sylius/Sylius/issues/19156) [FIX][2.2] Fix locking icons ([@jkindly](https://github.com/jkindly))

## v2.2.7 (2026-07-24)

#### Details

- [#19047](https://github.com/Sylius/Sylius/pull/19047) [Fix] Product Summary using wrong variant resolver ([@Prometee](https://github.com/Prometee))
- [#19023](https://github.com/Sylius/Sylius/pull/19023) [CS][DX] Refactor
- [#19056](https://github.com/Sylius/Sylius/pull/19056) [DX] Break footer menu and shipment general templates into smaller hooks 2.2 ([@rust-le](https://github.com/rust-le))
- [#19033](https://github.com/Sylius/Sylius/pull/19033) Filter out disabled products from get by code item endpoint ([@tomkalon](https://github.com/tomkalon))
- [#19057](https://github.com/Sylius/Sylius/pull/19057) [2.2] Fix SyliusUiBundle compiler passes priority ([@tomkalon](https://github.com/tomkalon))
- [#19068](https://github.com/Sylius/Sylius/pull/19068) Update UPGRADE-2.2.md ([@tomkalon](https://github.com/tomkalon))
- [#19046](https://github.com/Sylius/Sylius/pull/19046) Use ResourceClassResolver in IriConverter to fix discriminator subcla… ([@rust-le](https://github.com/rust-le))
- [#19059](https://github.com/Sylius/Sylius/pull/19059) Add Taxon documentation to OpenAPI specification ([@marekrzytki](https://github.com/marekrzytki))
- [#19087](https://github.com/Sylius/Sylius/pull/19087) [CS][DX] Refactor
- [#19085](https://github.com/Sylius/Sylius/pull/19085) [Addressing] Fix UTF-8 case-insensitive address comparison ([@Malina141](https://github.com/Malina141))
- [#19084](https://github.com/Sylius/Sylius/pull/19084) [CoreBundle] Guard against null shipping method in order eligibility validator ([@Wojdylak](https://github.com/Wojdylak))
- [#19050](https://github.com/Sylius/Sylius/pull/19050) [Bugfix] Recover order payment_state to "awaiting_payment" after authorized payment is cancelled ([@tomkalon](https://github.com/tomkalon))
- [#19107](https://github.com/Sylius/Sylius/pull/19107) [Composer] Conflict with api-platform/symfony 4.3.16 ([@GSadee](https://github.com/GSadee))
- [#19104](https://github.com/Sylius/Sylius/pull/19104) [Fix] [2.2] [AdminBundle] [UX] Compute shipment tracking placeholder width dynamically ([@crydotsnake](https://github.com/crydotsnake))
- [#19111](https://github.com/Sylius/Sylius/pull/19111) [CS][DX] Refactor
- [#19052](https://github.com/Sylius/Sylius/pull/19052) Prevent a coupon from invalidating its own promotion eligibility ([@TheMilek](https://github.com/TheMilek))
- [#19126](https://github.com/Sylius/Sylius/pull/19126) [Translations] add missing german translations for various bundles ([@crydotsnake](https://github.com/crydotsnake))
- [#19121](https://github.com/Sylius/Sylius/pull/19121) Add missing account_verification translations to de translations ([@shochdoerfer](https://github.com/shochdoerfer))
- [#19135](https://github.com/Sylius/Sylius/pull/19135) [2.2][FIX] Behat live component wait ([@jkindly](https://github.com/jkindly))
- [#19089](https://github.com/Sylius/Sylius/pull/19089) Prevent LiveComponents from interfering with modal behavior ([@tomkalon](https://github.com/tomkalon))
- [#19109](https://github.com/Sylius/Sylius/pull/19109) Fix infinity loop on taxon edit form ([@michalkaczmarek-bitbag](https://github.com/michalkaczmarek-bitbag))
- [#19150](https://github.com/Sylius/Sylius/pull/19150) [CI] Increase build timeout ([@jkindly](https://github.com/jkindly))
- [#19146](https://github.com/Sylius/Sylius/pull/19146) [ShopBundle] Fix displaying thank you page for guest users ([@michalkaczmarek-bitbag](https://github.com/michalkaczmarek-bitbag))
- [#19044](https://github.com/Sylius/Sylius/pull/19044) Fix German Swiss translation for no_label' ([@gebi84](https://github.com/gebi84))
- [#19123](https://github.com/Sylius/Sylius/pull/19123) Change translation for 'Kraje' to 'Krajiny' ([@elcuro](https://github.com/elcuro))
- [#19153](https://github.com/Sylius/Sylius/pull/19153) Lock webpack version to avoid issues with the latest release ([@bartek-sek](https://github.com/bartek-sek))

## v2.2.6 (2026-06-02)

#### Details

- [#18989](https://github.com/Sylius/Sylius/pull/18989) Fix modals appearing behind backdrop on sticky page-header ([@bartek-sek](https://github.com/bartek-sek))
- [#18990](https://github.com/Sylius/Sylius/pull/18990) Fix attribute card style for product show ([@shochdoerfer](https://github.com/shochdoerfer))
- [#18988](https://github.com/Sylius/Sylius/pull/18988) BUGFIX: Expose ShippingMethod *DeliveryTimeDays in admin API ([@daniellienert](https://github.com/daniellienert))
- [#19009](https://github.com/Sylius/Sylius/pull/19009) [ApiBundle][Tests] Add regression test for anonymous cart pickup wit… ([@Wojdylak](https://github.com/Wojdylak))
- [#19012](https://github.com/Sylius/Sylius/pull/19012) [ApiBundle] Fix "Undefined array key 0" in PathPrefixProvider when path equals API route ([@michalkaczmarek-bitbag](https://github.com/michalkaczmarek-bitbag))
- [#19001](https://github.com/Sylius/Sylius/pull/19001) Bugfix/csrf token ([@michalkaczmarek-bitbag](https://github.com/michalkaczmarek-bitbag))
- [#19017](https://github.com/Sylius/Sylius/pull/19017) Add appendError method to ResponseCheckerInterface ([@Prometee](https://github.com/Prometee))
- [#19018](https://github.com/Sylius/Sylius/pull/19018) [ApiBundle] Fix 404 on GET /shop/products/{code} when all associated products are disabled ([@michalkaczmarek-bitbag](https://github.com/michalkaczmarek-bitbag))
- [#19024](https://github.com/Sylius/Sylius/pull/19024) [2.2] [AttributeBundle] make Add and Delete button translatable in product attribute select type ([@crydotsnake](https://github.com/crydotsnake))
- [#19025](https://github.com/Sylius/Sylius/pull/19025) [API] Add regression tests for cross-customer cart item access ([@GSadee](https://github.com/GSadee))
- [#19026](https://github.com/Sylius/Sylius/pull/19026) [API] Slim down Swagger UI override and drop broken auto-auth JS ([@GSadee](https://github.com/GSadee))
- [#19038](https://github.com/Sylius/Sylius/pull/19038) [2.1] Prevent stale cart LiveComponents from mutating completed orders ([@TheMilek](https://github.com/TheMilek))
- [#19039](https://github.com/Sylius/Sylius/pull/19039) [2.1][API] Enforce channel eligibility check when changing payment method via account endpoint ([@TheMilek](https://github.com/TheMilek))
- [#19040](https://github.com/Sylius/Sylius/pull/19040) [2.1] Check payment request ownership ([@TheMilek](https://github.com/TheMilek))

## v2.2.5 (2026-04-10)

#### Details

- [#18579](https://github.com/Sylius/Sylius/pull/18579) Fix problem with empty taxon product index ([@tomkalon](https://github.com/tomkalon))
- [#18932](https://github.com/Sylius/Sylius/pull/18932) TASK: improve german translations for CH, DE, and AT ([@crydotsnake](https://github.com/crydotsnake))
- [#18933](https://github.com/Sylius/Sylius/pull/18933) fix: add default filter to breadcrumbs configuration title to prevent ScalarDataBag exception ([@camilleislasse](https://github.com/camilleislasse))
- [#18943](https://github.com/Sylius/Sylius/pull/18943) Fix build after APIPlatform 4.3.2 release ([@TheMilek](https://github.com/TheMilek))
- [#18941](https://github.com/Sylius/Sylius/pull/18941) Add payment enabled in channel validation ([@marekrzytki](https://github.com/marekrzytki))
- [#18926](https://github.com/Sylius/Sylius/pull/18926) [BUGFIX] Allow updating provinceName when provinceCode is null in API ([@rust-le](https://github.com/rust-le))
- [#18953](https://github.com/Sylius/Sylius/pull/18953) [BUGFIX] Make mailer services public to allow usage in state machine callbacks ([@rust-le](https://github.com/rust-le))
- [#18961](https://github.com/Sylius/Sylius/pull/18961) Resolve flash alert correctly without breaking translation ([@TheMilek](https://github.com/TheMilek))
- [#18940](https://github.com/Sylius/Sylius/pull/18940) Restore missing page titles for admin resource show pages ([@marekrzytki](https://github.com/marekrzytki))
- [#18922](https://github.com/Sylius/Sylius/pull/18922) Unified name translations handling in admin grid index ([@pbalcerzak](https://github.com/pbalcerzak))
- [#18965](https://github.com/Sylius/Sylius/pull/18965) Allow choosing different payment method with skip payment step when it gets disabled ([@TheMilek](https://github.com/TheMilek))
- [#18969](https://github.com/Sylius/Sylius/pull/18969) Add missing status code mapping for OrderItemNotFoundException ([@marekrzytki](https://github.com/marekrzytki))
- [#18970](https://github.com/Sylius/Sylius/pull/18970) Add email validation constraints to cart update ([@marekrzytki](https://github.com/marekrzytki))
- [#18972](https://github.com/Sylius/Sylius/pull/18972) Fix admin templates ([@loic425](https://github.com/loic425))
- [#18958](https://github.com/Sylius/Sylius/pull/18958) [API] Make API Platform resource command classes overridable via container parameters ([@Prometee](https://github.com/Prometee))
- [#18974](https://github.com/Sylius/Sylius/pull/18974) Unify Tests directory with tests ([@TheMilek](https://github.com/TheMilek))

## v2.2.4 (2026-03-18)

#### Details

- [#18904](https://github.com/sylius/sylius/issues/18904) [BUGFIX] remove redundant `object` from PHPDoc union types
- [#18899](https://github.com/sylius/sylius/issues/18899) [CS][DX] Refactor
- [#18898](https://github.com/sylius/sylius/issues/18898) [CS][DX] Refactor
- [#18895](https://github.com/sylius/sylius/issues/18895) [Admin] Fix product taxon grid `enabled` field always showing `true`
- [#18911](https://github.com/sylius/sylius/issues/18911) [BUGFIX] fix build errors
- [#18920](https://github.com/sylius/sylius/issues/18920) Telemetry improvements 2.1

## v2.2.3 (2026-03-09)

#### Details

- [#18747](https://github.com/Sylius/Sylius/pull/18747) Fix panther build ([@TheMilek](https://github.com/TheMilek))
- [#18758](https://github.com/Sylius/Sylius/pull/18758) Remove duplicated serialization group field ([@TheMilek](https://github.com/TheMilek))
- [#18785](https://github.com/Sylius/Sylius/pull/18785) Try to fix build after ResourceBundle release ([@TheMilek](https://github.com/TheMilek))
- [#18742](https://github.com/Sylius/Sylius/pull/18742) [Admin] Fix order history address fields not displaying empty values ([@Wojdylak](https://github.com/Wojdylak))
- [#18806](https://github.com/Sylius/Sylius/pull/18806) Fix after new release of PayumBundle ([@TheMilek](https://github.com/TheMilek))
- [#18836](https://github.com/Sylius/Sylius/pull/18836) Upgrade BuildTestAppAction from v3.0.1 to v4 ([@TheMilek](https://github.com/TheMilek))
- [#18832](https://github.com/Sylius/Sylius/pull/18832) [Admin] Fix images not being emitted with Webpack 5.105+ ([@GSadee](https://github.com/GSadee))
- [#18841](https://github.com/Sylius/Sylius/pull/18841) Fix Dutch translation for payment method ([@JordiDekker](https://github.com/JordiDekker))
- [#18871](https://github.com/Sylius/Sylius/pull/18871) Add conflict to api-platform/serializer 4.2.17 ([@TheMilek](https://github.com/TheMilek))
- [#18888](https://github.com/Sylius/Sylius/pull/18888) Remove redundant check with apip4.1.7 in matrix ([@TheMilek](https://github.com/TheMilek))
- [#18887](https://github.com/Sylius/Sylius/pull/18887) [BUGFIX] Make GitHub actions green again 2.1 ([@rust-le](https://github.com/rust-le))
- [#18844](https://github.com/Sylius/Sylius/pull/18844) Fix formatting in UPGRADE-2.0.md ([@LucaGallinari](https://github.com/LucaGallinari))

## v2.2.2 (2026-01-20)

#### Details

- [#18691](https://github.com/Sylius/Sylius/pull/18691) [CS][DX] Refactor
- [#18690](https://github.com/Sylius/Sylius/pull/18690) [CS][DX] Refactor
- [#18689](https://github.com/Sylius/Sylius/pull/18689) [CS][DX] Refactor
- [#18693](https://github.com/Sylius/Sylius/pull/18693) [CI] Standardize job timeouts to 15 minutes ([@Rafikooo](https://github.com/Rafikooo))
- [#18703](https://github.com/Sylius/Sylius/pull/18703) [Deps] Allow psr/http-message ^2.0 ([@Rafikooo](https://github.com/Rafikooo))
- [#18716](https://github.com/Sylius/Sylius/pull/18716) Try to fix build with PHP 8.5 and 8.4 ([@TheMilek](https://github.com/TheMilek))
- [#18707](https://github.com/Sylius/Sylius/pull/18707) [DX] Update branch aliases to 1.14-dev ([@Rafikooo](https://github.com/Rafikooo))
- [#18721](https://github.com/Sylius/Sylius/pull/18721) Fix tests application error templates ([@loic425](https://github.com/loic425))
- [#16892](https://github.com/Sylius/Sylius/pull/16892) [FIXTURES] Fix menu taxon code ([@TheMilek](https://github.com/TheMilek))
- [#18366](https://github.com/Sylius/Sylius/pull/18366) Fix ro PayumBundle Translation ([@revoltek-daniel](https://github.com/revoltek-daniel))
- [#18725](https://github.com/Sylius/Sylius/pull/18725) Bugfix/merged overrides missing operations ([@TheMilek](https://github.com/TheMilek))
- [#18702](https://github.com/Sylius/Sylius/pull/18702) [Composer] Remove outdated twig/twig conflicts from bundles ([@Rafikooo](https://github.com/Rafikooo))
- [#18722](https://github.com/Sylius/Sylius/pull/18722) [API] Payment Request fix default action when IRI is given ([@Prometee](https://github.com/Prometee))
- [#18732](https://github.com/Sylius/Sylius/pull/18732) Make PostgreSQL telemetry migration extend dedicated abstract ([@TheMilek](https://github.com/TheMilek))
- [#18369](https://github.com/Sylius/Sylius/pull/18369) Add form help rendering to forms ([@tomkalon](https://github.com/tomkalon))
- [#18735](https://github.com/Sylius/Sylius/pull/18735) [CS][DX] Refactor
- [#18726](https://github.com/Sylius/Sylius/pull/18726) [Channel] Resolve localhost equivalents when matching channel by hostname ([@Rafikooo](https://github.com/Rafikooo))
- [#18708](https://github.com/Sylius/Sylius/pull/18708) [DX] Improve AI contribution guidelines ([@Rafikooo](https://github.com/Rafikooo))
- [#18711](https://github.com/Sylius/Sylius/pull/18711) Fix typo in first_name.html.twig ([@LucaGallinari](https://github.com/LucaGallinari))
- [#18736](https://github.com/Sylius/Sylius/pull/18736) TASK: [2.2] [UiBundle] adjust translations for admin password reset page ([@crydotsnake](https://github.com/crydotsnake))
- [#18565](https://github.com/Sylius/Sylius/pull/18565) Add ternary operator to fix  related with empty key ([@michalkaczmarek-bitbag](https://github.com/michalkaczmarek-bitbag))
- [#18572](https://github.com/Sylius/Sylius/pull/18572) Bugfix/fix autocomplete in admin to be case insensitive ([@michalkaczmarek-bitbag](https://github.com/michalkaczmarek-bitbag))

## v2.2.1 (2025-12-18)

#### Details

- [#18680](https://github.com/Sylius/Sylius/pull/18680) [Telemetry] Fixes and improvements 1.14 ([@TheMilek](https://github.com/TheMilek))

## v2.2.0 (2025-12-17)

#### Details

- [#18365](https://github.com/Sylius/Sylius/pull/18365) [Feature] Prioritize default locale in TranslationLocaleProvider output ([@tomkalon](https://github.com/tomkalon))
- [#18412](https://github.com/Sylius/Sylius/pull/18412) [Maintenance] Raise minimal version of `api-platform/symfony` to `^4.2.1` ([@tomkalon](https://github.com/tomkalon))
- [#18410](https://github.com/Sylius/Sylius/pull/18410) [SHOP][FEATURE] Introduce address form modifiers in checkout component ([@tomkalon](https://github.com/tomkalon))
- [#18438](https://github.com/Sylius/Sylius/pull/18438) Remove phpspec/prophecy-phpunit dependency ([@Rafikooo](https://github.com/Rafikooo))
- [#18374](https://github.com/Sylius/Sylius/pull/18374) [CORE] Add priority to Zone ([@tomkalon](https://github.com/tomkalon))
- [#18546](https://github.com/Sylius/Sylius/pull/18546) [API] Return 422 instead of 400 for missing required fields ([@Rafikooo](https://github.com/Rafikooo))
- [#18544](https://github.com/Sylius/Sylius/pull/18544) [FEATURE] Add estimated delivery time functionality ([@Cholin2000](https://github.com/Cholin2000))
- [#18508](https://github.com/Sylius/Sylius/pull/18508) FEATURE: [2.2] [AdminBundle] make search menu in admin sidebar component hookable ([@crydotsnake](https://github.com/crydotsnake))
- [#18583](https://github.com/Sylius/Sylius/pull/18583) Mark method as deprecated because it is not used already ([@michalkaczmarek-bitbag](https://github.com/michalkaczmarek-bitbag))
- [#18614](https://github.com/Sylius/Sylius/pull/18614) [CS][DX] Refactor
- [#18594](https://github.com/Sylius/Sylius/pull/18594) [Maintenance] Bump Symfony to `^6.4 || ^7.4` ([@Rafikooo](https://github.com/Rafikooo))
- [#18568](https://github.com/Sylius/Sylius/pull/18568) [API][PaymentRequest] Rename payment request operations for shop context ([@Rafikooo](https://github.com/Rafikooo))
- [#18554](https://github.com/Sylius/Sylius/pull/18554) [AdminBundle] Allowing to use translation domain on menu ([@loic425](https://github.com/loic425))
- [#18454](https://github.com/Sylius/Sylius/pull/18454) FEAT: [2.2] [CoreBundle] extend sylius:install:setup command to set admin first/last name ([@crydotsnake](https://github.com/crydotsnake))
- [#15889](https://github.com/Sylius/Sylius/pull/15889) FEATURE: [2.2] [AdminBundle]  Introduce sylius:admin-user:delete CLI Command ([@crydotsnake](https://github.com/crydotsnake))
- [#15946](https://github.com/Sylius/Sylius/pull/15946) FEATURE: [2.2] [AdminBundle]  Introduce sylius:admin-user:list command ([@crydotsnake](https://github.com/crydotsnake))
- [#18646](https://github.com/Sylius/Sylius/pull/18646) [Maintenance] validation fixes ([@Cholin2000](https://github.com/Cholin2000))
- [#18654](https://github.com/Sylius/Sylius/pull/18654) [CS][DX] Refactor
- [#18647](https://github.com/Sylius/Sylius/pull/18647) [Composer] Bump dependencies + remove unused dev dependencies ([@GSadee](https://github.com/GSadee))
- [#18655](https://github.com/Sylius/Sylius/pull/18655) Add missing upgrade note to the 2.1 ([@GSadee](https://github.com/GSadee))
- [#18652](https://github.com/Sylius/Sylius/pull/18652) [CS][DX] Refactor
- [#18659](https://github.com/Sylius/Sylius/pull/18659) Add missing index to Order XML mapping ([@GSadee](https://github.com/GSadee))
- [#18661](https://github.com/Sylius/Sylius/pull/18661) Add config/reference.php to .gitignore ([@GSadee](https://github.com/GSadee))
- [#18662](https://github.com/Sylius/Sylius/pull/18662) Make new Zone priority field sortable by Gedmo ([@GSadee](https://github.com/GSadee))
- [#18669](https://github.com/Sylius/Sylius/pull/18669) Fix migrations skip commands 1.14 ([@TheMilek](https://github.com/TheMilek))
- [#18670](https://github.com/Sylius/Sylius/pull/18670) Remove Gedmo sortable-position from new Zone priority field ([@GSadee](https://github.com/GSadee))
- [#18672](https://github.com/Sylius/Sylius/pull/18672) Fix migrations skip commands 2.1 ([@TheMilek](https://github.com/TheMilek))

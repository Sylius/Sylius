# CHANGELOG FOR `2.2.X`

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

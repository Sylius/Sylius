# CHANGELOG FOR `1.3.X`

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

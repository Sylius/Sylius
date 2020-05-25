# CHANGELOG FOR `1.2.X`

## v1.2.17 (2019-05-07)

#### TL;DR

- Fixed the build and extracted some packages from Sylius core.

#### Details

- [#10259](https://github.com/Sylius/Sylius/issues/10259) [BuildFix] Ignore psalm annotations ([@Zales0123](https://github.com/Zales0123))
- [#10325](https://github.com/Sylius/Sylius/issues/10325) Extract Mailer component & bundle ([@pamil](https://github.com/pamil))
- [#10326](https://github.com/Sylius/Sylius/issues/10326) [WIP] Extract Grid component & bundle ([@pamil](https://github.com/pamil))
- [#10327](https://github.com/Sylius/Sylius/issues/10327) [WIP] Extract Resource component & bundle ([@pamil](https://github.com/pamil))
- [#10340](https://github.com/Sylius/Sylius/issues/10340) Fix #9646 by removing lambdas in JS file ([@tchapi](https://github.com/tchapi))

## v1.2.16 (2019-03-04)

#### TL;DR

- Fixed the build and extracted some packages from Sylius core

#### Details

- [#10182](https://github.com/Sylius/Sylius/issues/10182) Extract FixturesBundle ([@pamil](https://github.com/pamil))
- [#10184](https://github.com/Sylius/Sylius/issues/10184) Extract ThemeBundle ([@pamil](https://github.com/pamil))
- [#10188](https://github.com/Sylius/Sylius/issues/10188) Extract Registry component ([@pamil](https://github.com/pamil))

## v1.2.15 (2019-02-03)

## TL;DR

- This is the last bugfix release of the `1.2` branch

#### Details

- [#10118](https://github.com/Sylius/Sylius/issues/10118) [Product Review] fixed review validation when edited by admin ([@kboduch](https://github.com/kboduch))
- [#10119](https://github.com/Sylius/Sylius/issues/10119) Using channel code in shipping method configuration ([@nedac-sorbo](https://github.com/nedac-sorbo))
- [#10128](https://github.com/Sylius/Sylius/issues/10128) Syntax error in documentation ([@hatem20](https://github.com/hatem20))
- [#10132](https://github.com/Sylius/Sylius/issues/10132) Add missing Length constraint on product translation slug ([@venyii](https://github.com/venyii))
- [#10136](https://github.com/Sylius/Sylius/issues/10136) [HotFix] 500 on taxons list error fix (, [@Zales0123](https://github.com/Zales0123))

## v1.2.14 (2019-01-17)

#### TL;DR

- Added support for overriding templates from plugins ([#10082](https://github.com/Sylius/Sylius/issues/10082))

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
- [#10059](https://github.com/Sylius/Sylius/issues/10059) Cover specs with PHPStan ([@pamil](https://github.com/pamil))
- [#10061](https://github.com/Sylius/Sylius/issues/10061) GUS existence mentioned in Sylius installation guide ([@bartoszpietrzak1994](https://github.com/bartoszpietrzak1994))
- [#10063](https://github.com/Sylius/Sylius/issues/10063) Do not require clearing cache when changing ResourceBundle drivers or metadata classes ([@pamil](https://github.com/pamil))
- [#10065](https://github.com/Sylius/Sylius/issues/10065) Remove unused Behat method ([@Zales0123](https://github.com/Zales0123))
- [#10070](https://github.com/Sylius/Sylius/issues/10070) #9699 Fix for viewing products when they belong to a taxon and to one… ([@laurent35240](https://github.com/laurent35240))
- [#10072](https://github.com/Sylius/Sylius/issues/10072) It's 2019! ([@bartoszpietrzak1994](https://github.com/bartoszpietrzak1994))
- [#10076](https://github.com/Sylius/Sylius/issues/10076) [Docs] Remove vagrant references ([@lchrusciel](https://github.com/lchrusciel))
- [#10077](https://github.com/Sylius/Sylius/issues/10077) Fix select attributes according to recent Symfony form changes ([@Zales0123](https://github.com/Zales0123))
- [#10081](https://github.com/Sylius/Sylius/issues/10081) [CoreBundle] Fix Type in Construct for ChannelDeletionListener ([@Donjohn](https://github.com/Donjohn))
- [#10082](https://github.com/Sylius/Sylius/issues/10082) [Theme] Allow overriding templates from plugins (1.2.*) ([@Zales0123](https://github.com/Zales0123), [@pamil](https://github.com/pamil))
- [#10086](https://github.com/Sylius/Sylius/issues/10086) Remove container cleanup in test environment (1.2) ([@Zales0123](https://github.com/Zales0123))
- [#10088](https://github.com/Sylius/Sylius/issues/10088) Fix GridBundle build ([@Zales0123](https://github.com/Zales0123))
- [#10093](https://github.com/Sylius/Sylius/issues/10093) Typo ([@OskarStark](https://github.com/OskarStark))
- [#10094](https://github.com/Sylius/Sylius/issues/10094) Overriding plugin templates in themes tests ([@Zales0123](https://github.com/Zales0123))
- [#10095](https://github.com/Sylius/Sylius/issues/10095) Fix build failing due to newest twig version ([@Zales0123](https://github.com/Zales0123))
- [#10096](https://github.com/Sylius/Sylius/issues/10096) fix link ([@OskarStark](https://github.com/OskarStark))
- [#10097](https://github.com/Sylius/Sylius/issues/10097) less noise ([@OskarStark](https://github.com/OskarStark))
- [#10100](https://github.com/Sylius/Sylius/issues/10100) [Documentation] Visually mark most of the component&bundle docs outdated ([@kulczy](https://github.com/kulczy), [@CoderMaggie](https://github.com/CoderMaggie))

## v1.2.13 (2018-12-17)

#### TL;DR

- Fixed compatibility issues with Symfony 4.1.18 and 4.1.19 ([#10020](https://github.com/Sylius/Sylius/issues/10020), [#10038](https://github.com/Sylius/Sylius/issues/10038))

#### Details

- [#9837](https://github.com/Sylius/Sylius/issues/9837) Repaired shipping method fixture ([@JakobTolkemit](https://github.com/JakobTolkemit))
- [#9919](https://github.com/Sylius/Sylius/issues/9919) Fix for promotion of 100 percent with coupon ([@laurent35240](https://github.com/laurent35240))
- [#9975](https://github.com/Sylius/Sylius/issues/9975) Ignore locale request restriction for profiler and it's toolbar ([@Peteck](https://github.com/Peteck))
- [#9985](https://github.com/Sylius/Sylius/issues/9985) Add missing code and calculator mandatory field on tax rate documenation ([@Soullivaneuh](https://github.com/Soullivaneuh))
- [#9997](https://github.com/Sylius/Sylius/issues/9997) Fix typo cookbook about emails ([@Konafets](https://github.com/Konafets))
- [#9998](https://github.com/Sylius/Sylius/issues/9998) Improve the ShippingBundle doc ([@Konafets](https://github.com/Konafets))
- [#10011](https://github.com/Sylius/Sylius/issues/10011) [Kernel] Move WebServerBundle to dev/test environment ([@GSadee](https://github.com/GSadee))
- [#10012](https://github.com/Sylius/Sylius/issues/10012) Fixed incorrect Behat MinkExtension key in the docs ([@jzawadzki](https://github.com/jzawadzki))
- [#10016](https://github.com/Sylius/Sylius/issues/10016) Column 'position' cannot be null ([@zspine](https://github.com/zspine))
- [#10020](https://github.com/Sylius/Sylius/issues/10020) [HotFix][BuildFix] Use old PhpMatcherDumper to avoid trailing slash problems ([@Zales0123](https://github.com/Zales0123))
- [#10023](https://github.com/Sylius/Sylius/issues/10023) Remove billingAddress and shippingAddress ([@Konafets](https://github.com/Konafets))
- [#10029](https://github.com/Sylius/Sylius/issues/10029) Fix type annotation for $addToCartCommand ([@daniellienert](https://github.com/daniellienert))
- [#10038](https://github.com/Sylius/Sylius/issues/10038) Fix the build on 1.3 by more flexible router overriding ([@pamil](https://github.com/pamil))

## v1.2.12 (2018-11-28)

#### TL;DR

- Security fixes according to [problems](https://github.com/dominictarr/event-stream/issues/116) with `dominictarr/event-stream` library
- Hot-fix preventing installation of `symfony/symfony:4.1.8` due to Behat tests problems

#### Details

- [#9860](https://github.com/Sylius/Sylius/issues/9860) [Behat] Viewing errors ([@loic425](https://github.com/loic425))
- [#9932](https://github.com/Sylius/Sylius/issues/9932) [Phpspec] add a missing scenario on customer context spec ([@loic425](https://github.com/loic425))
- [#9937](https://github.com/Sylius/Sylius/issues/9937) [Payum] Add missing model interfaces ([@GSadee](https://github.com/GSadee))
- [#9945](https://github.com/Sylius/Sylius/issues/9945) Fix for 9942 ([@igormukhingmailcom](https://github.com/igormukhingmailcom))
- [#9950](https://github.com/Sylius/Sylius/issues/9950) [Docs][Book] Promotion priorities ([@CoderMaggie](https://github.com/CoderMaggie))
- [#9955](https://github.com/Sylius/Sylius/issues/9955) Remove inline css ([@Prometee](https://github.com/Prometee))
- [#9963](https://github.com/Sylius/Sylius/issues/9963) [Docs][OrderBundle] Remove old, incorrect docs ([@CoderMaggie](https://github.com/CoderMaggie))
- [#9970](https://github.com/Sylius/Sylius/issues/9970) [Hot-fix] Make build great again ([@Zales0123](https://github.com/Zales0123))
- [#9972](https://github.com/Sylius/Sylius/issues/9972) Update gulp-livereload ([@kulczy](https://github.com/kulczy))

## v1.2.11 (2018-11-16)

#### Details

- [#9885](https://github.com/Sylius/Sylius/issues/9885) fixed ad blocking issue ([@loevgaard](https://github.com/loevgaard))
- [#9887](https://github.com/Sylius/Sylius/issues/9887) use behat page object extension ([@loic425](https://github.com/loic425))
- [#9898](https://github.com/Sylius/Sylius/issues/9898) #9862 Number of items in order summary page ([@laurent35240](https://github.com/laurent35240))
- [#9906](https://github.com/Sylius/Sylius/issues/9906) Product images deletion fix ([@Zales0123](https://github.com/Zales0123))
- [#9908](https://github.com/Sylius/Sylius/issues/9908) [Documentation] Add new styles ([@kulczy](https://github.com/kulczy))
- [#9910](https://github.com/Sylius/Sylius/issues/9910) [Composer] Update ApiTestCase ([@lchrusciel](https://github.com/lchrusciel))
- [#9922](https://github.com/Sylius/Sylius/issues/9922) Apply coding standard fixes from SyliusLabs/CodingStandard ^3.0@dev ([@pamil](https://github.com/pamil))
- [#9923](https://github.com/Sylius/Sylius/issues/9923) Use oneline phpdocs for property type info ([@pamil](https://github.com/pamil))
- [#9926](https://github.com/Sylius/Sylius/issues/9926) Fix plugin naming convention documentation ([@Zales0123](https://github.com/Zales0123))
- [#9927](https://github.com/Sylius/Sylius/issues/9927) Fix version widget and add better quality logo ([@kulczy](https://github.com/kulczy))
- [#9929](https://github.com/Sylius/Sylius/issues/9929) Update SyliusLabs/CodingStandard to ^3.0 ([@pamil](https://github.com/pamil))

## v1.2.10 (2018-11-07)

#### Details

- [#9854](https://github.com/Sylius/Sylius/issues/9854) Update installation.rst ([@zghosts](https://github.com/zghosts))
- [#9856](https://github.com/Sylius/Sylius/issues/9856) #9694 Do not show bulk sections and checkboxes if bulk actions are di… ([@laurent35240](https://github.com/laurent35240))
- [#9866](https://github.com/Sylius/Sylius/issues/9866) [Order] Changing function typing ([@Roshyo](https://github.com/Roshyo))
- [#9883](https://github.com/Sylius/Sylius/issues/9883) Hydrate promotion_rules directly on loading active promotions for a channel (1n) ([@stefandoorn](https://github.com/stefandoorn))
- [#9892](https://github.com/Sylius/Sylius/issues/9892) [Order] Removing after SM callback ([@Roshyo](https://github.com/Roshyo))
- [#9900](https://github.com/Sylius/Sylius/issues/9900) Fix typos in BDD Transformers docs ([@sarjon](https://github.com/sarjon))

## v1.2.9 (2018-10-24)

#### Details

- [#9796](https://github.com/Sylius/Sylius/pull/9796) Improve product attributes JS (@Zales0123)
- [#9827](https://github.com/Sylius/Sylius/pull/9827) Custom homepage controller as public service (@davidroberto)
- [#9829](https://github.com/Sylius/Sylius/pull/9829) Wrong usage of returned data (@Prometee)
- [#9832](https://github.com/Sylius/Sylius/pull/9832) Fix gulp uglify error with arrow functions (@magentix)
- [#9839](https://github.com/Sylius/Sylius/pull/9839) [Docs] How to disable admin notifications (@stefandoorn)

## v1.2.8 (2018-10-11)

#### Details

- [#8093](https://github.com/Sylius/Sylius/pull/8093) [Order] Fixed sylius:remove-expired-carts help (@sweoggy)
- [#8494](https://github.com/Sylius/Sylius/pull/8494) set gender `u` as default value - resolves #8493 (@pamil, @kochen)
- [#9627](https://github.com/Sylius/Sylius/pull/9627) Narrow down selectors to prevent unexpected bugs (@teohhanhui)
- [#9646](https://github.com/Sylius/Sylius/pull/9646) [Admin][Product edit] Change the value of the taxons individually when checked/unchecked. (@sbarbat)
- [#9685](https://github.com/Sylius/Sylius/pull/9685) Update gulpfile.babel.js (@mihaimitrut)
- [#9727](https://github.com/Sylius/Sylius/pull/9727) Do not stale issues selected to Roadmap (@Zales0123)
- [#9741](https://github.com/Sylius/Sylius/pull/9741) [Travis] validate yaml files (@loic425)
- [#9742](https://github.com/Sylius/Sylius/pull/9742) [Behat] Changing my account password with token I received scenario (@loic425)
- [#9743](https://github.com/Sylius/Sylius/pull/9743) Update shipments.rst (@hmonglee)
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
- [#9769](https://github.com/Sylius/Sylius/pull/9769) [Behat] Add scenarios on resetting password validation feature (@loic425)
- [#9772](https://github.com/Sylius/Sylius/pull/9772) Fix doubled province id on checkout addressing page (@pamil)
- [#9774](https://github.com/Sylius/Sylius/pull/9774) Ask for confirmation when cancelling an order (@pamil)
- [#9775](https://github.com/Sylius/Sylius/pull/9775) Limit products shown in associated products autocomplete field (@pamil)
- [#9776](https://github.com/Sylius/Sylius/pull/9776) [Core] Make implicit dependency explicit (@pamil)
- [#9783](https://github.com/Sylius/Sylius/pull/9783) Correct grammar mistake in README (@pamil)
- [#9791](https://github.com/Sylius/Sylius/pull/9791) [Docs] Update year in copyright (@CoderMaggie)
- [#9803](https://github.com/Sylius/Sylius/pull/9803) `purge_mode` has been rename to `mode` (@Prometee)
- [#9805](https://github.com/Sylius/Sylius/pull/9805) [Shop] Fix password request & contact pages with a mobile view. (@versgui)

## v1.2.7 (2018-09-24)

#### Details

- [#9683](https://github.com/Sylius/Sylius/pull/9683) Do not run tests if not needed (@pamil)
- [#9687](https://github.com/Sylius/Sylius/pull/9687) [Core][Fixture] Fix for ignored geographical zone scope (@SebLours)
- [#9691](https://github.com/Sylius/Sylius/pull/9691) Fixing Typo in Documentation (@bhargavmehta)
- [#9700](https://github.com/Sylius/Sylius/pull/9700) Allow to use Pagerfanta in both ^1.0 and ^2.0 (@pamil)
- [#9708](https://github.com/Sylius/Sylius/pull/9708) Fix typo in Behat feature (@stefandoorn)
- [#9709](https://github.com/Sylius/Sylius/pull/9709) Fix typo in filename (@stefandoorn)
- [#9714](https://github.com/Sylius/Sylius/pull/9714) Fix docs build (@pamil)
- [#9724](https://github.com/Sylius/Sylius/pull/9724) PHPSpec version not specified in docs (@Zales0123)

## v1.2.6 (2018-08-27)

#### Details

- [#9635](https://github.com/Sylius/Sylius/pull/9635) Updated a word in documentation to read better (@mbklnd)
- [#9662](https://github.com/Sylius/Sylius/pull/9662) Fix Grids archive ability to work on multi-param urls (@diimpp)
- [#9667](https://github.com/Sylius/Sylius/pull/9667) [UI] Fix icons on checkout (@lchrusciel)

## v1.2.5 (2018-08-13)

#### TL;DR

- Database migrations support MySQL 8 ([#9622](https://github.com/Sylius/Sylius/pull/9622))

#### Details

- [#9622](https://github.com/Sylius/Sylius/pull/9622) Quote row_number identifier for MySQL queries (@alcaeus)
- [#9624](https://github.com/Sylius/Sylius/pull/9624) Fix missing "required" class on some form fields (@teohhanhui)
- [#9634](https://github.com/Sylius/Sylius/pull/9634) [Core] Fix OrderItemNamesSetter specification (@Zales0123)
- [#9642](https://github.com/Sylius/Sylius/pull/9642) [Currency] Improve currency specs (@loic425)

## v1.2.4 (2018-07-27)

#### TL;DR

- There's a new [plugin development guide](https://docs.sylius.com/en/1.1/plugins/plugin-development-guide/index.html) ([#9592](https://github.com/Sylius/Sylius/pull/9592))
- Fixed compatibility with PHP-PM ([#9613](https://github.com/Sylius/Sylius/pull/9613), [#9608](https://github.com/Sylius/Sylius/pull/9608))
- Fixed buggy shop user removal in the admin panel ([#9618](https://github.com/Sylius/Sylius/pull/9618))

#### Details

- [#9193](https://github.com/Sylius/Sylius/pull/9193) [Documentation][GridBundle]Describes sorting, filtering by sub entity properties (@Mipme)
- [#9289](https://github.com/Sylius/Sylius/pull/9289) Check if customer is set before get/set email (@pamil, @teohhanhui)
- [#9352](https://github.com/Sylius/Sylius/pull/9352) Minor fixes to grammar (@gregsomers)
- [#9421](https://github.com/Sylius/Sylius/pull/9421) Field used for label does not exist (@pamil, @psihius)
- [#9553](https://github.com/Sylius/Sylius/pull/9553) Fix wrong type for "images" option in ProductFixture (@teohhanhui)
- [#9563](https://github.com/Sylius/Sylius/pull/9563) [AdminApi] Fix command (@lchrusciel)
- [#9578](https://github.com/Sylius/Sylius/pull/9578) [Core] Nullable customer on order (@lchrusciel)
- [#9580](https://github.com/Sylius/Sylius/pull/9580) [User] Fix bc-break in UserLastLoginSubscriber (@lchrusciel)
- [#9587](https://github.com/Sylius/Sylius/pull/9587) Adding strict typing for PHP classes in images doc (@Roshyo)
- [#9590](https://github.com/Sylius/Sylius/pull/9590) Ensure that DatabaseSetupCommandsProvider::getDatabaseName() returns a string. (@azjezz)
- [#9592](https://github.com/Sylius/Sylius/pull/9592) Plugin development guide v1.0 (@Zales0123)
- [#9599](https://github.com/Sylius/Sylius/pull/9599) [Behat] Grammar fix (@lchrusciel)
- [#9600](https://github.com/Sylius/Sylius/pull/9600) Pull request template fix (@lchrusciel)
- [#9603](https://github.com/Sylius/Sylius/pull/9603) [Maintenance] Move github templates (@lchrusciel)
- [#9608](https://github.com/Sylius/Sylius/pull/9608) Remove instances of loop.index0 (@dnna, @pamil)
- [#9611](https://github.com/Sylius/Sylius/pull/9611) [Doc] Fix service name for custom taxation calculator (@dannyvw)
- [#9612](https://github.com/Sylius/Sylius/pull/9612) Handle null email in oauth login (@dnna)
- [#9613](https://github.com/Sylius/Sylius/pull/9613) Fix ShopBasedCartContext resetting (@dnna)
- [#9617](https://github.com/Sylius/Sylius/pull/9617) Fix CS and add tests for ShopBasedCartContext (@pamil)
- [#9618](https://github.com/Sylius/Sylius/pull/9618) Reproduce CSRF token validation failure when deleting an user in admin panel (@pamil)
- [#9620](https://github.com/Sylius/Sylius/pull/9620) [docs] updating taxon models documentation (@loic425, @pamil)

## v1.2.3 (2018-07-10)

#### TL;DR

- Fixing the application after not-so-perfect security issue fix in the last release

#### Details

- [See the diff since the last patch release](https://github.com/Sylius/Sylius/compare/v1.2.2...v1.2.3)

## v1.2.2 (2018-07-08)

#### TL;DR

- **SECURITY FIX:** Added CSRF protection for the following action:
  
    - marking order's payment as completed
    - marking order's payment as refunded
    - marking product review as accepted
    - marking product review as rejected

#### Details

- [#9475](https://github.com/Sylius/Sylius/pull/9475) Make Stalebot less annoying (@Zales0123)
- [#9491](https://github.com/Sylius/Sylius/pull/9491) [Documentation] Document Forum in the support section (@CoderMaggie)
- [#9515](https://github.com/Sylius/Sylius/pull/9515) [Documentation] Fix typos (@adrienlucas)
- [#9558](https://github.com/Sylius/Sylius/pull/9558) Use ...Prototype() instead of prototype('...') in Symfony configuration (@pamil)


## v1.2.1 (2018-07-05)

#### TL;DR

- It's no longer required to put Sylius bundles before Doctrine Bundle ([#9527](https://github.com/Sylius/Sylius/pull/9527))
- There's an official plugins list in README ([#9493](https://github.com/Sylius/Sylius/pull/9493))
- ResourceBundle CRUD routing generator works with bundleless templates ([#9534](https://github.com/Sylius/Sylius/pull/9534))

#### Details

- [#9340](https://github.com/Sylius/Sylius/pull/9340) the name of file was wrong in docu (@amirkoklan)
- [#9345](https://github.com/Sylius/Sylius/pull/9345) [HOTFIX] Missing configuration for channel in sonata (@lchrusciel)
- [#9487](https://github.com/Sylius/Sylius/pull/9487) Improve use of Semantic's cards in frontend (@mbabker)
- [#9488](https://github.com/Sylius/Sylius/pull/9488) Describe upgrade process for 1.1.x -> 1.2.0 (@pamil)
- [#9493](https://github.com/Sylius/Sylius/pull/9493) Document officially supported plugins in the README (@pamil)
- [#9527](https://github.com/Sylius/Sylius/pull/9527) [ResourceBundle] Fix DoctrineTargetEntitiesResolverPass priority to avoid mapping issues. (@adrienlucas)
- [#9534](https://github.com/Sylius/Sylius/pull/9534) [ResourceBundle] fix routing templates for sf4 (@loic425)
- [#9537](https://github.com/Sylius/Sylius/pull/9537) [Admin] Add missing form parameter to sonata form events (@GSadee)
- [#9539](https://github.com/Sylius/Sylius/pull/9539) [minor] SCA (@kalessil)
- [#9540](https://github.com/Sylius/Sylius/pull/9540) PHPStan 0.10 upgrade & road to level 2 checks (@pamil)
- [#9541](https://github.com/Sylius/Sylius/pull/9541) Require Symfony 4.1.1 and remove hotfixes for 4.1.0 (@pamil)
- [#9545](https://github.com/Sylius/Sylius/pull/9545) Remove duplicated copyright note (@enekochan)
- [#9546](https://github.com/Sylius/Sylius/pull/9546) Added title to product reviews, fixes #9425 (@richardjohn, @adrienlucas, @Zales0123)
- [#9548](https://github.com/Sylius/Sylius/pull/9548) Unify catch block in ShopBasedCartContext (@pamil)
- [#9550](https://github.com/Sylius/Sylius/pull/9550) Mention roadmap in README (@pamil)
- [#9552](https://github.com/Sylius/Sylius/pull/9552) Lower PHPStan level 2 errors from 222 to 9 (@pamil)
- [#9555](https://github.com/Sylius/Sylius/pull/9555) Add Sylius/CustomerOrderCancellationPlugin to the list of official plugins (@pamil)

## v1.2.0 (2018-06-12)

## TL;DR

- Added hotfixes for Symfony 4.1.0 ([#9476](https://github.com/Sylius/Sylius/pull/9476))

#### Details

- [#9418](https://github.com/Sylius/Sylius/pull/9418) Update Model.ProductOption.yml (@severino32)
- [#9419](https://github.com/Sylius/Sylius/pull/9419) Moved IE css polyfills (@czende)
- [#9424](https://github.com/Sylius/Sylius/pull/9424) Lazy load Doctrine event listeners (@teohhanhui)
- [#9461](https://github.com/Sylius/Sylius/pull/9461) Added note about LiipImagineBundle upgrade (@sweoggy)
- [#9464](https://github.com/Sylius/Sylius/pull/9464) Fixed typo in PayumController (@qkdreyer)
- [#9465](https://github.com/Sylius/Sylius/pull/9465) [Documentation] Fix deprecated link to repository (@CoderMaggie)
- [#9466](https://github.com/Sylius/Sylius/pull/9466) Document "event" option in resource routing (@Zales0123)
- [#9467](https://github.com/Sylius/Sylius/pull/9467) Update outdated method prototype('array') with arrayPrototype() (@jafaronly)
- [#9470](https://github.com/Sylius/Sylius/pull/9470) [Documentation] Updated link to Payum docs (@pogorivan)
- [#9476](https://github.com/Sylius/Sylius/pull/9476) Enhance workarounds while waiting for Symfony 4.1.1 (@pamil)
- [#9477](https://github.com/Sylius/Sylius/pull/9477) Remove labels descriptions in docs (@pamil)
- [#9480](https://github.com/Sylius/Sylius/pull/9480) [docs] Use `app/config/routing/admin.yml` everywhere (@gido)

## v1.2.0-RC (2018-06-07)

#### TL;DR

- Added support for Symfony ^4.1 ([#9454](https://github.com/Sylius/Sylius/pull/9454))
- Dropped support for Symfony 4.0 ([#9454](https://github.com/Sylius/Sylius/pull/9454))
- Added ability to use custom services as factories / repositories in ResourceBundle ([#9422](https://github.com/Sylius/Sylius/pull/9422), [#9442](https://github.com/Sylius/Sylius/pull/9442))
- Improved default shipping method resolving ([#9398](https://github.com/Sylius/Sylius/pull/9398))

#### Details

- [#9398](https://github.com/Sylius/Sylius/pull/9398) [Core] Default shipping method basing on category fix (@Zales0123, @pamil, @stefandoorn)
- [#9422](https://github.com/Sylius/Sylius/pull/9422) Add ability to use a custom service as factory (@pamil, @pjedrzejewski)
- [#9436](https://github.com/Sylius/Sylius/pull/9436) [Addressing]  Fix default validation groups of AddressType (@vvasiloi)
- [#9440](https://github.com/Sylius/Sylius/pull/9440) Fix secret parameter resolving (@pamil)
- [#9441](https://github.com/Sylius/Sylius/pull/9441) Remove vendorPath command-line argument from root gulpfile (@teohhanhui)
- [#9442](https://github.com/Sylius/Sylius/pull/9442) Add an ability to use custom resource repositories (@pamil)
- [#9444](https://github.com/Sylius/Sylius/pull/9444) [Documentation] Fixed typo in note about --force-with-lease flag (@pmikolajek)
- [#9454](https://github.com/Sylius/Sylius/pull/9454) Symfony 4.1 support (together with dropping Symfony 4.0 support) (@pamil, @Zales0123)
- [#9456](https://github.com/Sylius/Sylius/pull/9456) Add documentation for using custom repository service in ResourceBundle (@pamil)
- [#9458](https://github.com/Sylius/Sylius/pull/9458) [docs] fix server:start command (@hiousi)
- [#9462](https://github.com/Sylius/Sylius/pull/9462) Remove two redundant services from CoreBundle (@jafaronly)
- [#9463](https://github.com/Sylius/Sylius/pull/9463) Update platform-sh.rst (@antonioperic)

## v1.2.0-BETA (2018-05-28)

#### TL;DR 

- Symfony 4 support ([#9062](https://github.com/Sylius/Sylius/issues/9062))
- Liip/ImagineBundle requirement changed from `^1.9` to `^2.0` ([#9380](https://github.com/Sylius/Sylius/pull/9380))
- Introduced Babel and Gulp 4 in our frontend toolset ([#9405](https://github.com/Sylius/Sylius/pull/9405))

#### Details

- [#8629](https://github.com/Sylius/Sylius/pull/8629) [Shipping][OrderProcessing] Default shipping method fixes (@Zales0123)
- [#9019](https://github.com/Sylius/Sylius/pull/9019) Return event response for initialize update event (@dannyvw)
- [#9162](https://github.com/Sylius/Sylius/pull/9162) Update all occurrences of .dev to .test (@jackbentley)
- [#9185](https://github.com/Sylius/Sylius/pull/9185) [ResourceBundle] Add the controller tag (@dragosprotung)
- [#9212](https://github.com/Sylius/Sylius/pull/9212) [Reviews] nullable title for reviews (@loic425)
- [#9255](https://github.com/Sylius/Sylius/pull/9255) Changes form channel color from text to color input type (@Tetragramat)
- [#9306](https://github.com/Sylius/Sylius/pull/9306) [Resource] Make sure Sylius resources services are public (@Zales0123)
- [#9308](https://github.com/Sylius/Sylius/pull/9308) [Adjustment] Inject adjustment types that shall be cleared (@Zales0123)
- [#9324](https://github.com/Sylius/Sylius/pull/9324) ChannelNotFoundException updated to other exceptions' format without BC break (#9324) (@bartoszpietrzak1994)
- [#9330](https://github.com/Sylius/Sylius/pull/9330) Disable deprecated "form mapping" feature in SonataCoreBundle (@teohhanhui)
- [#9366](https://github.com/Sylius/Sylius/pull/9366) [BC BREAK] Symfony 4.0 compatibility, part #1 (@pamil)
- [#9372](https://github.com/Sylius/Sylius/pull/9372) Make subpackages compatible with Symfony 4 (@pamil)
- [#9373](https://github.com/Sylius/Sylius/pull/9373) Make application compatible with not-yet-released Mink release (@pamil)
- [#9377](https://github.com/Sylius/Sylius/pull/9377) Make packages require ^1.2 packages (@pamil)
- [#9379](https://github.com/Sylius/Sylius/pull/9379) Replace outdated "Symfony2" with "Symfony" in package descriptions (@pamil)
- [#9380](https://github.com/Sylius/Sylius/pull/9380) Use stable Liip/ImagineBundle ^2.0 (@pamil)
- [#9382](https://github.com/Sylius/Sylius/pull/9382) Define commands as services (@pamil)
- [#9383](https://github.com/Sylius/Sylius/pull/9383) Use %kernel.project_dir% instead of %kernel.root_dir% (@pamil)
- [#9385](https://github.com/Sylius/Sylius/pull/9385) Fix %kernel.project_dir% directory usage (@pamil)
- [#9386](https://github.com/Sylius/Sylius/pull/9386) Use stable Mink BrowserKit driver (@pamil)
- [#9405](https://github.com/Sylius/Sylius/pull/9405) Upgrade to gulp 4 (@teohhanhui)
- [#9410](https://github.com/Sylius/Sylius/pull/9410) Fixed missing orderby in product variants (@Bencsi)
- [#9426](https://github.com/Sylius/Sylius/pull/9426) Random Symfony 4 related fixes (@pamil)
- [#9428](https://github.com/Sylius/Sylius/pull/9428) Symfony 4: Behat scenarios isolation + random fixes (@pamil)
- [#9429](https://github.com/Sylius/Sylius/pull/9429) Require passing build for Symfony 4 on Travis (@pamil)

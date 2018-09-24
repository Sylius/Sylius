# CHANGELOG FOR `1.1.X`

## v1.1.14 (2018-09-24)

#### Details

- [#9687](https://github.com/Sylius/Sylius/pull/9687) [Core][Fixture] Fix for ignored geographical zone scope (@SebLours)
- [#9700](https://github.com/Sylius/Sylius/pull/9700) Allow to use Pagerfanta in both ^1.0 and ^2.0 (@pamil)
- [#9683](https://github.com/Sylius/Sylius/pull/9683) Do not run tests if not needed (@pamil)
- [#9709](https://github.com/Sylius/Sylius/pull/9709) Fix typo in filename (@stefandoorn)
- [#9708](https://github.com/Sylius/Sylius/pull/9708) Fix typo in Behat feature (@stefandoorn)
- [#9714](https://github.com/Sylius/Sylius/pull/9714) Fix docs build (@pamil)
- [#9691](https://github.com/Sylius/Sylius/pull/9691) Fixing Typo in Documentation (@bhargavmehta)

## v1.1.13 (2018-08-27)

#### Details

- [#9635](https://github.com/Sylius/Sylius/pull/9635) Updated a word in documentation to read better (@mbklnd)
- [#9662](https://github.com/Sylius/Sylius/pull/9662) Fix Grids archive ability to work on multi-param urls (@diimpp)
- [#9667](https://github.com/Sylius/Sylius/pull/9667) [UI] Fix icons on checkout (@lchrusciel)

## v1.1.12 (2018-08-13)

#### TL;DR

- Database migrations support MySQL 8 ([#9622](https://github.com/Sylius/Sylius/pull/9622))

#### Details

- [#9622](https://github.com/Sylius/Sylius/pull/9622) Quote row_number identifier for MySQL queries (@alcaeus)
- [#9624](https://github.com/Sylius/Sylius/pull/9624) Fix missing "required" class on some form fields (@teohhanhui)
- [#9634](https://github.com/Sylius/Sylius/pull/9634) [Core] Fix OrderItemNamesSetter specification (@Zales0123)

## v1.1.11 (2018-07-27)

#### TL;DR

- There's a new [plugin development guide](http://docs.sylius.com/en/1.1/plugins/plugin-development-guide/index.html) ([#9592](https://github.com/Sylius/Sylius/pull/9592))
- Fixed compatibility with PHP-PM ([#9613](https://github.com/Sylius/Sylius/pull/9613), [#9608](https://github.com/Sylius/Sylius/pull/9608))
- Fixed buggy shop user removal in the admin panel ([#9618](https://github.com/Sylius/Sylius/pull/9618))

#### Details

- [#9193](https://github.com/Sylius/Sylius/pull/9193) [Documentation][GridBundle]Describes sorting, filtering by sub entity properties (@Mipme)
- [#9289](https://github.com/Sylius/Sylius/pull/9289) Check if customer is set before get/set email (@pamil, @teohhanhui)
- [#9352](https://github.com/Sylius/Sylius/pull/9352) Minor fixes to grammar (@gregsomers)
- [#9421](https://github.com/Sylius/Sylius/pull/9421) Field used for label does not exist (@pamil, @psihius)
- [#9553](https://github.com/Sylius/Sylius/pull/9553) Fix wrong type for "images" option in ProductFixture (@teohhanhui)
- [#9578](https://github.com/Sylius/Sylius/pull/9578) [Core] Nullable customer on order (@lchrusciel)
- [#9580](https://github.com/Sylius/Sylius/pull/9580) [User] Fix bc-break in UserLastLoginSubscriber (@lchrusciel)
- [#9587](https://github.com/Sylius/Sylius/pull/9587) Adding strict typing for PHP classes in images doc (@Roshyo)
- [#9590](https://github.com/Sylius/Sylius/pull/9590) Ensure that DatabaseSetupCommandsProvider::getDatabaseName() returns a string. (@azjezz)
- [#9592](https://github.com/Sylius/Sylius/pull/9592) Plugin development guide v1.0 (@Zales0123)
- [#9599](https://github.com/Sylius/Sylius/pull/9599) [Behat] Grammar fix (@lchrusciel)
- [#9600](https://github.com/Sylius/Sylius/pull/9600) Pull request template fix (@lchrusciel)
- [#9603](https://github.com/Sylius/Sylius/pull/9603) [Maintenance] Move github templates (@lchrusciel)
- [#9608](https://github.com/Sylius/Sylius/pull/9608) Remove instances of loop.index0 (@dnna)
- [#9611](https://github.com/Sylius/Sylius/pull/9611) [Doc] Fix service name for custom taxation calculator (@dannyvw)
- [#9612](https://github.com/Sylius/Sylius/pull/9612) Handle null email in oauth login (@dnna)
- [#9613](https://github.com/Sylius/Sylius/pull/9613) Fix ShopBasedCartContext resetting (@dnna)
- [#9617](https://github.com/Sylius/Sylius/pull/9617) Fix CS and add tests for ShopBasedCartContext (@pamil)
- [#9618](https://github.com/Sylius/Sylius/pull/9618) Reproduce CSRF token validation failure when deleting an user in admin panel (@pamil)
- [#9620](https://github.com/Sylius/Sylius/pull/9620) [docs] updating taxon models documentation (@loic425)

## v1.1.10 (2018-07-10)

#### TL;DR

- Fixing the application after not-so-perfect security issue fix in the last release

#### Details

- [See the diff since the last patch release](https://github.com/Sylius/Sylius/compare/v1.1.9...v1.1.10)

## v1.1.9 (2018-07-08)

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

## v1.1.8 (2018-07-05)

#### TL;DR

- It's no longer required to put Sylius bundles before Doctrine Bundle ([#9527](https://github.com/Sylius/Sylius/pull/9527))
- There's an official plugins list in README ([#9493](https://github.com/Sylius/Sylius/pull/9493))

#### Details

- [#9340](https://github.com/Sylius/Sylius/pull/9340) the name of file was wrong in docu (@amirkoklan)
- [#9345](https://github.com/Sylius/Sylius/pull/9345) [HOTFIX] Missing configuration for channel in sonata (@lchrusciel)
- [#9487](https://github.com/Sylius/Sylius/pull/9487) Improve use of Semantic's cards in frontend (@mbabker)
- [#9493](https://github.com/Sylius/Sylius/pull/9493) Document officially supported plugins in the README (@pamil)
- [#9527](https://github.com/Sylius/Sylius/pull/9527) [ResourceBundle] Fix DoctrineTargetEntitiesResolverPass priority to avoid mapping issues. (@adrienlucas)
- [#9537](https://github.com/Sylius/Sylius/pull/9537) [Admin] Add missing form parameter to sonata form events (@GSadee)
- [#9539](https://github.com/Sylius/Sylius/pull/9539) [minor] SCA (@kalessil)
- [#9540](https://github.com/Sylius/Sylius/pull/9540) PHPStan 0.10 upgrade & road to level 2 checks (@pamil)
- [#9546](https://github.com/Sylius/Sylius/pull/9546) Added title to product reviews, fixes #9425 (@richardjohn, @adrienlucas, @Zales0123)
- [#9548](https://github.com/Sylius/Sylius/pull/9548) Unify catch block in ShopBasedCartContext (@pamil)
- [#9550](https://github.com/Sylius/Sylius/pull/9550) Mention roadmap in README (@pamil)
- [#9552](https://github.com/Sylius/Sylius/pull/9552) Lower PHPStan level 2 errors from 222 to 9 (@pamil)
- [#9555](https://github.com/Sylius/Sylius/pull/9555) Add Sylius/CustomerOrderCancellationPlugin to the list of official plugins (@pamil)

## v1.1.7 (2018-06-12)

#### TL;DR

- Lots of bugfixes

#### Details

- [#9418](https://github.com/Sylius/Sylius/pull/9418) Update Model.ProductOption.yml (@severino32)
- [#9419](https://github.com/Sylius/Sylius/pull/9419) Moved IE css polyfills (@czende)
- [#9424](https://github.com/Sylius/Sylius/pull/9424) Lazy load Doctrine event listeners (@teohhanhui)
- [#9436](https://github.com/Sylius/Sylius/pull/9436) [Addressing]  Fix default validation groups of AddressType (@vvasiloi)
- [#9440](https://github.com/Sylius/Sylius/pull/9440) Fix secret parameter resolving (@pamil)
- [#9444](https://github.com/Sylius/Sylius/pull/9444) [Documentation] Fixed typo in note about --force-with-lease flag (@pmikolajek)
- [#9462](https://github.com/Sylius/Sylius/pull/9462) Remove two redundant services from CoreBundle (@jafaronly)
- [#9463](https://github.com/Sylius/Sylius/pull/9463) Update platform-sh.rst (@antonioperic)
- [#9464](https://github.com/Sylius/Sylius/pull/9464) Fixed typo in PayumController (@qkdreyer)
- [#9465](https://github.com/Sylius/Sylius/pull/9465) [Documentation] Fix deprecated link to repository (@CoderMaggie)
- [#9466](https://github.com/Sylius/Sylius/pull/9466) Document "event" option in resource routing (@Zales0123)
- [#9467](https://github.com/Sylius/Sylius/pull/9467) Update outdated method prototype('array') with arrayPrototype() (@jafaronly)
- [#9470](https://github.com/Sylius/Sylius/pull/9470) [Documentation] Updated link to Payum docs (@pogorivan)
- [#9477](https://github.com/Sylius/Sylius/pull/9477) Remove labels descriptions in docs (@pamil)
- [#9480](https://github.com/Sylius/Sylius/pull/9480) [docs] Use `app/config/routing/admin.yml` everywhere (@gido)

## v1.1.6 (2018-05-21)

- [#9310](https://github.com/Sylius/Sylius/pull/9310) Declare a separate ImagesUploadListener service (@teohhanhui)
- [#9328](https://github.com/Sylius/Sylius/pull/9328) Fix invalid YAML tag syntax (@teohhanhui)
- [#9334](https://github.com/Sylius/Sylius/pull/9334) Quote usage of !!int  to remove deprecation warning (@stefandoorn)
- [#9335](https://github.com/Sylius/Sylius/pull/9335) Make sure controller services are public (@teohhanhui)
- [#9339](https://github.com/Sylius/Sylius/pull/9339) Fix product repository's service id in docs (@hectorj)
- [#9344](https://github.com/Sylius/Sylius/pull/9344) Fix exception controller config (@teohhanhui)
- [#9349](https://github.com/Sylius/Sylius/pull/9349) [Grid] Default parameters for "default" grid action (@Zales0123)
- [#9350](https://github.com/Sylius/Sylius/pull/9350) Readme enhancements (@pamil)
- [#9354](https://github.com/Sylius/Sylius/pull/9354) Order update error flash message typo (@czende)
- [#9356](https://github.com/Sylius/Sylius/pull/9356) [Components] Fix links for documentation in readme (@Jibbarth)
- [#9362](https://github.com/Sylius/Sylius/pull/9362) Add reset method to DataCollectors, needed for SF4 compat (@jordisala1991)
- [#9371](https://github.com/Sylius/Sylius/pull/9371) Fix sourcemaps (@teohhanhui)
- [#9378](https://github.com/Sylius/Sylius/pull/9378) Make Sylius 1.1 packages require other Sylius packages in at least that version (@pamil)
- [#9379](https://github.com/Sylius/Sylius/pull/9379) Replace outdated "Symfony2" with "Symfony" in package descriptions (@pamil)
- [#9397](https://github.com/Sylius/Sylius/pull/9397) Add missing replacements to composer.json (@jordisala1991)
- [#9404](https://github.com/Sylius/Sylius/pull/9404) Update node-sass for compatibility with Node.js 10 (@teohhanhui)

## v1.1.5 (2018-04-13)

- [#9323](https://github.com/Sylius/Sylius/pull/9323) formatting content in a email message (@axzx)
- [#9322](https://github.com/Sylius/Sylius/pull/9322) Make build passing again (@pamil)
- [#9316](https://github.com/Sylius/Sylius/pull/9316) Correct minor typo in docs (@cedricziel)
- [#9315](https://github.com/Sylius/Sylius/pull/9315) [DOC] Minor documentation changes for product reviews (@cedricziel)
- [#9314](https://github.com/Sylius/Sylius/pull/9314) Minor doc fix (@cedricziel)
- [#9312](https://github.com/Sylius/Sylius/pull/9312) Fix JS error when autocomplete field is empty (@teohhanhui)
- [#9308](https://github.com/Sylius/Sylius/pull/9308) [Adjustment] Inject adjustment types that shall be cleared (@Zales0123)
- [#9303](https://github.com/Sylius/Sylius/pull/9303) HOTFIX: Do not require NelmioAliceBundle in shared kernel (@pamil)
- [#9302](https://github.com/Sylius/Sylius/pull/9302) willdurand/hateoas 2.12 version update (#9302) (@bartoszpietrzak1994)
- [#9300](https://github.com/Sylius/Sylius/pull/9300) Outdated method reference removed from docs (@bartoszpietrzak1994)
- [#9295](https://github.com/Sylius/Sylius/pull/9295) Cleanup PrioritizedCompositeServicePass definition name (@diimpp)
- [#9284](https://github.com/Sylius/Sylius/pull/9284) [Grid] Filtering orders bug (@Zales0123)
- [#9268](https://github.com/Sylius/Sylius/pull/9268) Documentation - Use nullable return type (@Holicz)

## v1.1.4 (2018-04-04)

- [#9301](https://github.com/Sylius/Sylius/pull/9301) HOTFIX: Do not require FidryAliceDataFixturesBundle in shared kernel (@pamil)

## v1.1.3 (2018-04-04)

- [#9298](https://github.com/Sylius/Sylius/pull/9298) Define conflicts with incompatible dependencies versions (@pamil)
- [#9287](https://github.com/Sylius/Sylius/pull/9287) Fix wrong CustomerInterface type in PHPDoc (@teohhanhui)
- [#9266](https://github.com/Sylius/Sylius/pull/9266) Update ApiTestCase to ^3.0 (@Zales0123)
- [#9281](https://github.com/Sylius/Sylius/pull/9281) [Behat] Handle multiple notifications in NotificationChecker (@Zales0123)
- [#9264](https://github.com/Sylius/Sylius/pull/9264) [Documentation][CookBook] Specific SyliusBundles extension (@Zales0123, @Adraesh)
- [#9267](https://github.com/Sylius/Sylius/pull/9267) Add PaymentMethod::instructions option to fixtures (@stefandoorn)
- [#9269](https://github.com/Sylius/Sylius/pull/9269) Fix wrong Balrog of Morgoth name (@Zales0123)

## v1.1.2 (2018-03-16)

- [#9265](https://github.com/Sylius/Sylius/pull/9265) Run PHPStan in Travis CI (@pamil)
- [#9260](https://github.com/Sylius/Sylius/pull/9260) Remove "incenteev/composer-parameter-handler" from packages dependencies (@pamil)
- [#9259](https://github.com/Sylius/Sylius/pull/9259) Various composer.json enhancements (@pamil)
- [#9256](https://github.com/Sylius/Sylius/pull/9256) PHPUnit ^6.5 for packages (@pamil)
- [#9248](https://github.com/Sylius/Sylius/pull/9248) Update to PHPUnit ^6.5 (@pamil)
- [#9247](https://github.com/Sylius/Sylius/pull/9247) Update to ApiTestCase ^2.0 and PHPUnit ^6.0 (@pamil)
- [#9244](https://github.com/Sylius/Sylius/pull/9244) Remove composer.lock (@pamil)
- [#9246](https://github.com/Sylius/Sylius/pull/9246) Mention the need of applying migrations when upgrading 1.0 -> 1.1 (@stefandoorn)
- [#9135](https://github.com/Sylius/Sylius/pull/9135) Fixed Customer API docs with invalid "groups" parameter (@gorkalaucirica)
- [#9176](https://github.com/Sylius/Sylius/pull/9176) [Documentation] Customizing Factory (@GitProdEnv)
- [#9237](https://github.com/Sylius/Sylius/pull/9237) Fix select province in checkout address (@serieznyi)
- [#9233](https://github.com/Sylius/Sylius/pull/9233) [AttributeBundle] Fixing composer.json for ramsey/uuid (@David-Crty)
- [#9235](https://github.com/Sylius/Sylius/pull/9235) [ResourceBundle] make controller public by default (@bendavies)
- [#9236](https://github.com/Sylius/Sylius/pull/9236) [ResourceBundle] make sylius.translatable_entity_locale_assigner public (@bendavies)
- [#9238](https://github.com/Sylius/Sylius/pull/9238) Make tests green again (@pamil)
- [#9219](https://github.com/Sylius/Sylius/pull/9219) Improve flags support (@shadypierre)
- [#9194](https://github.com/Sylius/Sylius/pull/9194) [Documentation][Cookbook]Adding validation for image uploads (@Mipme)
- [#9211](https://github.com/Sylius/Sylius/pull/9211) Bring extra care for the documentation! (@pamil)
- [#9181](https://github.com/Sylius/Sylius/pull/9181) Remove surprising redundant x sign from docblock (@damonsson)

## v1.1.1 (2018-02-26)

- [#9195](https://github.com/Sylius/Sylius/pull/9195) [Documentation][GridBundle]Wrong definition of sortable attribute (@Mipme)
- [#9145](https://github.com/Sylius/Sylius/pull/9145) Run Travis with readonly project directories (@pamil)
- [#9206](https://github.com/Sylius/Sylius/pull/9206) [Documentation] Translatable model - Fix titles and add template support (@shadypierre)
- [#9204](https://github.com/Sylius/Sylius/pull/9204) Fix invalid use of count by checking for an array (@venyii)
- [#9189](https://github.com/Sylius/Sylius/pull/9189) [Documentation] Add more rules to the contribution guide + Fix CMS cookbook (@CoderMaggie)
- [#9192](https://github.com/Sylius/Sylius/pull/9192) [Documentation] Add 1.1 branch to Contribution guides (@CoderMaggie)
- [#9188](https://github.com/Sylius/Sylius/pull/9188) [Behat] Remove some unused methods in Behat pages (@Zales0123)
- [#9155](https://github.com/Sylius/Sylius/pull/9155) [Documentation] Add CMS integration cookbook (@bitbager)

## v1.1.0 (2018-02-09)

- [#9165](https://github.com/Sylius/Sylius/pull/9165) Make accordion in select attribute values great again (@Zales0123)
- [#9163](https://github.com/Sylius/Sylius/pull/9163) Product reviews API semantic fix (@Zales0123)
- [#9158](https://github.com/Sylius/Sylius/pull/9158) Update KnpGaufretteBundle to ^0.5 (@pamil)

## v1.1.0-RC (2018-02-02)

- [#9129](https://github.com/Sylius/Sylius/pull/9129) Add symfony/thanks Composer plugin (@pamil)
- [#9084](https://github.com/Sylius/Sylius/pull/9084) Minor enhancements to product review API pull request (@pamil)
- [#9013](https://github.com/Sylius/Sylius/pull/9013) Restrict scanning for composer.json in themes to certain directory depth (optional) (@stefandoorn)
- [#8772](https://github.com/Sylius/Sylius/pull/8772) Product reviews API (@paulstoica)
- [#9082](https://github.com/Sylius/Sylius/pull/9082) Remove strict declaration on migration (@stefandoorn)
- [#9081](https://github.com/Sylius/Sylius/pull/9081) Add index on Channel::hostname to prevent table scan on each request (@stefandoorn)
- [#9070](https://github.com/Sylius/Sylius/pull/9070) Extend Travis build matrix & setup extra jobs running nightly (1.1) (@pamil)
- [#9040](https://github.com/Sylius/Sylius/pull/9040) Try to auto-detect a bundle's model namespace by default (@mbabker)
- [#9063](https://github.com/Sylius/Sylius/pull/9063) Require Symfony ^3.4 in components & bundles (@pamil)
- [#9061](https://github.com/Sylius/Sylius/pull/9061) Require Symfony ^3.4 (@pamil)
- [#8940](https://github.com/Sylius/Sylius/pull/8940) Change bulk action implementation and remove the need for a BC break (@pamil)
- [#8491](https://github.com/Sylius/Sylius/pull/8491) [Admin] Mass deletion on admin grid (fixes #93) (@GSadee, @stefandoorn)
- [#8874](https://github.com/Sylius/Sylius/pull/8874) [Order][OrderItem] Immutable product/variant names (@GSadee, @johnrisby)
- [#8766](https://github.com/Sylius/Sylius/pull/8766) [ProductAttribute] Make select attribute translatable (@GSadee, @Lowlo)
- [#8680](https://github.com/Sylius/Sylius/pull/8680) add sylius version to the footer in admin (@gabiudrescu)
- [#8843](https://github.com/Sylius/Sylius/pull/8843) Allow to use environmental variables to populate parameters (@pamil)
- [#8817](https://github.com/Sylius/Sylius/pull/8817) Change master branch version to 1.1.0-DEV (@pamil)
- [#8798](https://github.com/Sylius/Sylius/pull/8798) [Installation] Add setting a username during installation (@GSadee)
- [#8714](https://github.com/Sylius/Sylius/pull/8714) Set up upgrade file for 1.1 (@pamil)
- [#8682](https://github.com/Sylius/Sylius/pull/8682) Gitignore webserver pid files (@gabiudrescu)
- [#8675](https://github.com/Sylius/Sylius/pull/8675) Treat `dev-master` as 1.1 (@pamil)
- [#8662](https://github.com/Sylius/Sylius/pull/8662) fix link to BitBager PayUPlugin (@kochen)

# CHANGELOG FOR `1.2.X`

## v1.2.7 (2018-09-24)

#### Details

- [#9683](https://github.com/Sylius/Sylius/pull/9683) Do not run tests if not needed (@pamil)
- [#9687](https://github.com/Sylius/Sylius/pull/9687) [Core][Fixture] Fix for ignored geographical zone scope (@SebLours)
- [#9691](https://github.com/Sylius/Sylius/pull/9691) Fixing Typo in Documentation (@bhargavmehta)
- [#9700](https://github.com/Sylius/Sylius/pull/9700) Allow to use Pagerfanta in both ^1.0 and ^2.0 (@pamil)
- [#9708](https://github.com/Sylius/Sylius/pull/9708) Fix typo in Behat feature (@stefandoorn)
- [#9709](https://github.com/Sylius/Sylius/pull/9709) Fix typo in filename (@stefandoorn)
- [#9714](https://github.com/Sylius/Sylius/pull/9714) Fix docs build (@pamil)

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

- There's a new [plugin development guide](http://docs.sylius.com/en/1.1/plugins/plugin-development-guide/index.html) ([#9592](https://github.com/Sylius/Sylius/pull/9592))
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

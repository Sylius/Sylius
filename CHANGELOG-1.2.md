# CHANGELOG FOR `1.2.X`

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
-

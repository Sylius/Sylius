# CHANGELOG FOR `1.3.X`

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

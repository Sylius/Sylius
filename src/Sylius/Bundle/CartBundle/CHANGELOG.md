CHANGELOG
=========

### v0.3.0 (2013-xx-xx)

* Remove `CartOperator` & `CartOperatorInterface`,
* Introduce `SyliusCartEvents` & event listeners.
* Removed the ``sylius_cart`` prefix from services and models, used ``sylius`` instead.

### v0.2.0 (2012-12-27)

* Introduce default cart entity,
* Use Doctrine RTEL to map interfaces instead of real entities,
* Rename `CartController::showAction` to `CartController::summaryAction`,
* Renamed `SyliusCartBundle:Cart:show.html` template to ``SyliusCartBundle:Cart:summary.html`,
* Add base controller.

### v0.1.0 (2012-12-05)

* First development release,
* Introduced ItemResolvingException,
* More complete set of [phpspec2](http://phpspec.net) examples,
* Changed configuration schema,
* Bundle now uses [SyliusResourceBundle](http://github.com/Sylius/SyliusResourceBundle) for model persistence,
* Models now depend on Doctrine collections,
* New controller,
* Renamed **Item** to **CartItem**,
* Renamed **ItemType** to **CartItemType**,
* Introduce specs with [phpspec2](http://phpspec.net),
* Renamed **CartFormType** to **CartType** to be consistent.

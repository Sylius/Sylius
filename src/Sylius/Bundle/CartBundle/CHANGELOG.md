CHANGELOG
=========

### v0.6.0

* Release before components introduction.

### v0.5.0

* Symfony 2.3 compatible version.
* Removed `Entity` classes.
* Based on SyliusSalesBundle order model.

### v0.4.0

* Last Symfony 2.2 compatible version.

### v0.3.0

* Remove `CartOperator` & `CartOperatorInterface`.
* Introduce `SyliusCartEvents` & event listeners.
* Removed the ``sylius_cart`` prefix from services and models, used ``sylius`` instead.
* All money values are represented as integers.

### v0.2.0

* Introduce default cart entity.
* Use Doctrine RTEL to map interfaces instead of real entities.
* Rename `CartController::showAction` to `CartController::summaryAction`.
* Renamed `SyliusCartBundle:Cart:show.html` template to ``SyliusCartBundle:Cart:summary.html`.
* Add base controller.

### v0.1.0

* First development release.
* Introduced ItemResolvingException.
* More complete set of [phpspec2](http://phpspec.net) examples.
* Changed configuration schema.
* Bundle now uses [SyliusResourceBundle](http://github.com/Sylius/SyliusResourceBundle) for model persistence.
* Models now depend on Doctrine collections.
* New controller.
* Renamed **Item** to **CartItem**.
* Renamed **ItemType** to **CartItemType**.
* Introduce specs with [phpspec2](http://phpspec.net).
* Renamed **CartFormType** to **CartType** to be consistent.

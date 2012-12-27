CHANGELOG
=========

### v0.2.0

* Introduce default cart entity.
* Use Doctrine RTEL to map interfaces instead of real entities.
* Rename ``CartController::showAction`` to ``CartController::summaryAction``.
* Renamed ``SyliusCartBundle:Cart:show.html`` template to ``SyliusCartBundle:Cart:summary.html``.
* Add base controller.

### v0.1.0

* First development release.
* Introduced ItemResolvingException.
* More complete set of [phpspec2](http://phpspec.net) examples.

### 02-12-2012

* Changed configuration schema.

### 01-11-2012

* Bundle now uses [SyliusResourceBundle](http://github.com/Sylius/SyliusResourceBundle) for model persistence.
* Models now depend on Doctrine collections.
* New controller.
* Renamed **Item** to **CartItem**.
* Renamed **ItemType** to **CartItemType**.
* Introduce specs with [phpspec2](http://phpspec.net).

### 28-05-2012

* Renamed **CartFormType** to **CartType** to be consistent.

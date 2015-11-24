CHANGELOG
=========

### v0.10.0

* Twig extension was renamed from `SyliusMoneyExtension` into `MoneyExtension`.
* Twig extension was renamed from `SyliusRestrictedZoneExtension` into `RestrictedZoneExtension`,
  also the service name was changed from `sylius.twig.restricted_zone_extension`
  to `sylius.twig.extension.restricted_zone`.

### v0.16.0

* Replaced custom  CalculatorRegistry with ServiceRegistry component in ShippingMethodTypeSpec

* Add FormRegistryInterface in let in ShippingMethodTypeSpec
CHANGELOG
=========

### v0.9.0

* Release before the components extraction.
* Translate calculator labels.

### v0.8.0

* Convert translations to YAML.
* Add shipAction() to Shipment controller.

### v0.7.0

* Add weight based shipping calculator.
* Various minor fixes.

### v0.6.0

* Release before components introduction. (delayed)

### v0.5.0

* Release after repositories reorganization.

### v0.1.0

* Initial dev release.
* Remove entities, use dynamic mapping.
* Introduce shipping rules system.

### 12-03-2013

* Use SyliusMoneyBundle and integers to represent charges.

### 30-01-2013

* Remove the ``sylius_shipping`` prefix from services and models.
  Use simple ``sylius`` instead.
* Tag ``sylius_shipping.calculator`` was renamed to ``sylius.shipping_calculator``.

### v0.16.0

* Change service sylius.shipping_calculator_registry to sylius.registry.shipping_calculator
  and sylius.shipping_rule_checker_registry to sylius.registry.shipping_rule_checker
* Change service id for shipping rule checker registry to sylius.registry.shipping_rule_checker in RegisterRuleCheckersPass,
  RegisterRuleCheckersPassSpec
* Change id of service to sylius.registry.shipping_calculator in RegisterCalculatorsPassSpec, RegisterCalculatorsPass
* Replace custom RuleCheckerRegistry with ServiceRegistry in BuildRuleFormSubscriber, RuleType, ShippingMethodType,
  BuildRuleFormSubscriberSpec, RuleTypeSpec, ShippingMethodTypeSpec
* Replace custom CalculatorRegistry with ServiceRegistry in BuildShippingMethodFormSubscriber, ShippingMethodChoiceType, ShippingMethodType,
  BuildShippingMethodFormSubscriberSpec, ShippingMethodChoiceTypeSpec

* Add new field FormRegistryInterface to ShippingMethod in SyliusShippingExtension
* Change name of calculators forms according to this schema: sylius.form.type_shipping_calculator_<typeOfCalculator> in service.xml
* Change name of calculator types according to this schema : sylius_shipping_calculator_<typeOfCalculator> in class from Type/Calculator
* BuildMethodFromSubscriber - add new filed FormRegistryInterface, rework addConfigurationFields method
* ShippingMethodType - is constructed with new parameter FormRegistryInterface, rework buildForm

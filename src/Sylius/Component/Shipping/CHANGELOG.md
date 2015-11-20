CHANGELOG
=========

### v0.10.0

* Initial dev release.

### v0.16.0

* Remove `Calculator`
* All calculators class implements `CalculatorInterface`

* `CalculatorRegistry` was deleted and replaced by `ServiceRegistry`
* `RuleCheckerRegistry` was deleted adn replaced by `ServiceRegistry`
* CalculatorRegistryInterface, ExistingCalculatorException, NonExistingCalculatorException, ExistingRuleCheckerExceptionSpec,
  ExistingRuleCheckerExceptionSpec, NonExistingRuleCheckerException, NonExistingRuleCheckerExceptionSpec, RuleCheckerRegistrySpec,
  RuleCheckerRegistryInterface were deleted
* Change type of registry to `ServiceRegistryInterface` in `DelegatingCalculator`, `ShippingMethodEligibilityChecker`,
  `ShippingMethodEligibilityCheckerSpec`, `DelegatingCalculatorSpec`


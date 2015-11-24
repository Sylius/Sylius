CHANGELOG
=========

### v0.10.0

* Initial dev release.

### v0.16.0

* Remove `Calculator`
* All calculators class implements `CalculatorInterface`

* `CalculatorRegistry` was deleted and replaced by `ServiceRegistry`
* `RuleCheckerRegistry` was deleted and replaced by `ServiceRegistry`
* CalculatorRegistryInterface, ExistingCalculatorException, NonExistingCalculatorException, ExistingRuleCheckerExceptionSpec,
  ExistingRuleCheckerExceptionSpec, NonExistingRuleCheckerException, NonExistingRuleCheckerExceptionSpec, RuleCheckerRegistrySpec,
  RuleCheckerRegistryInterface were deleted
* Change type of registry to `ServiceRegistryInterface` in `DelegatingCalculator`, `ShippingMethodEligibilityChecker`,
  `ShippingMethodEligibilityCheckerSpec`, `DelegatingCalculatorSpec`

* Rework CalculatorInterface, now it has only two methods : getType and calculate.
* All calculators have new type, which is accordant with calculator rate type, for example : "per_item_rate".
* Remove getConfiguration and isConfigurable form all calculators.
* Rework specs according to above changes.

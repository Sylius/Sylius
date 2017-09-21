# UPGRADE FROM 1.0 to 1.1

### Core / CoreBundle

* Added `ChannelInterface::getDefaultCustomerTaxCategory` and `ChannelInterface::setDefaultCustomerTaxCategory` signatures
* Added `TaxRateInterface::getCustomerTaxCategory` and `TaxRateInterface::setCustomerTaxCategory` signatures
* Added `CustomerGroup` and `CustomerGroupInterface` with methods`getTaxCategory` and `setTaxCategory`
* Taxation process was changed to use customer tax category:
    
    * `OrderTaxesApplicatorInterface::apply(OrderInterface $order, ZoneInterface $zone)` signature was changed to `OrderTaxesApplicatorInterface::apply(OrderInterface $order, ZoneInterface $zone, CustomerTaxCategoryInterface $customerTaxCategory)`
    * `TaxCalculationStrategyInterface::applyTaxes(OrderInterface $order, ZoneInterface $zone)` signature was changed to `TaxCalculationStrategyInterface::applyTaxes(OrderInterface $order, ZoneInterface $zone, CustomerTaxCategoryInterface $customerTaxCategory)`
    * `TaxCalculationStrategyInterface::supports(OrderInterface $order, ZoneInterface $zone)` signature was changed to `TaxCalculationStrategyInterface::supports(OrderInterface $order, ZoneInterface $zone, CustomerTaxCategoryInterface $customerTaxCategory)`

@managing_shipping_methods
Feature: Archiving obsolete shipping methods
    In order to hide no longer available shipping methods from the list and customers' use
    As an Administrator
    I want to archive shipping methods

    Background:
        Given the store operates on a single channel in "United States"
        And the store allows shipping with "UPS Carrier" and "FedEx Carrier"
        And I am logged in as an administrator

    @domain
    Scenario: Archiving a shipping method does not remove it from the database
        When I archive the "UPS Carrier" shipping method
        Then the shipping method "UPS Carrier" should still exist in the registry

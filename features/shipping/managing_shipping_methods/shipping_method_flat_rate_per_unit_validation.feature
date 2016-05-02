@managing_shipping_methods
Feature: Shipping method flat rate per unit calculator validation
    In order to avoid making mistakes in a flat rate per unit calculator of shipping method
    As an Administrator
    I want to be prevented from adding it without specifying amount

    Background:
        Given the store is available in "English (United States)"
        And the store has a base currency "Euro"
        And there is a zone "EU" containing all members of the European Union
        Given I am logged in as an administrator

    @ui @javascript
    Scenario: Trying to add a new shipping method with flat rate per unit calculator without specifying its amount
        Given I want to create a new shipping method
        And I name it "FedEx Carrier" in "English (United States)"
        But I do not specify amount for "Flat rate per unit" calculator
        When I try to add it
        Then I should be notified that amount should not be blank
        And shipping method with name "FedEx Carrier" should not be added

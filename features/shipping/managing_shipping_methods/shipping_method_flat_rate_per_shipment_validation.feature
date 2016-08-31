@managing_shipping_methods
Feature: Shipping method flat rate per shipment calculator validation
    In order to avoid making mistakes in a flat rate per shipment calculator of shipping method
    As an Administrator
    I want to be prevented from adding it without specifying amount

    Background:
        Given the store operates on a single channel in "United States"
        And the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Trying to add a new shipping method with flat rate per shipment calculator without specifying its amount
        Given I want to create a new shipping method
        And I name it "FedEx Carrier" in "English (United States)"
        But I do not specify amount for "Flat rate per shipment" calculator
        When I try to add it
        Then I should be notified that amount should not be blank
        And shipping method with name "FedEx Carrier" should not be added

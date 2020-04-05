@managing_shipping_methods
Feature: Shipping method flat rate per unit calculator validation
    In order to avoid making mistakes in a flat rate per unit calculator of shipping method
    As an Administrator
    I want to be prevented from adding it without specifying amount

    Background:
        Given the store operates on a channel named "Web" in "USD" currency
        And the store is available in "English (United States)"
        And the store has a zone "United States" with code "US"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Trying to add a new shipping method with flat rate per unit calculator without specifying its amount
        When I want to create a new shipping method
        And I specify its code as "FED_EX_CARRIER"
        And I name it "FedEx Carrier" in "English (United States)"
        And I define it for the "United States" zone
        But I do not specify amount for "Flat rate per unit" calculator
        When I try to add it
        Then I should be notified that amount for "Web" channel should not be blank
        And shipping method with name "FedEx Carrier" should not be added

    @ui @javascript
    Scenario: Trying to add a new shipping method with flat rate per unit calculator with charge below 0
        When I want to create a new shipping method
        And I specify its code as "FED_EX_CARRIER"
        And I name it "FedEx Carrier" in "English (United States)"
        And I define it for the "United States" zone
        And I choose "Flat rate per unit" calculator
        And I specify its amount as "-50.00" for "Web" channel
        When I try to add it
        Then I should be notified that shipping charge for "Web" channel cannot be lower than 0
        And shipping method with name "FedEx Carrier" should not be added

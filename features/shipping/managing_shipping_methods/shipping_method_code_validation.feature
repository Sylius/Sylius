@managing_shipping_methods
Feature: Shipping method code validation
    In order to avoid making mistakes when managing a shipping method
    As an Administrator
    I want to be prevented from adding it with invalid code

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store is available in "English (United States)"
        And the store has a zone "United States" with code "US"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a new shipping method with
        Given I want to create a new shipping method
        When I name it "FedEx Carrier" in "English (United States)"
        And I specify its code as "#π/"
        And I try to add it
        Then I should be notified that code needs to contain only specific symbols
        And shipping method with name "FedEx Carrier" should not be added

    @ui
    Scenario: Trying to add a new shipping method with
        Given I want to create a new shipping method
        When I name it "FedEx Carrier" in "English (United States)"
        And I specify its code as "PEC  -PEC"
        And I try to add it
        Then I should be notified that code needs to contain only specific symbols
        And shipping method with name "FedEx Carrier" should not be added

    @ui @javascript
    Scenario: Trying to add a new shipping method with
        Given I want to create a new shipping method
        When I name it "FedEx Carrier First US Division" in "English (United States)"
        And I specify its code as "PEC-US_01"
        And I define it for the "United States" zone
        And I choose "Flat rate per shipment" calculator
        And I specify its amount as 50 for "Web-US" channel
        And I add it
        Then the shipping method "FedEx Carrier First US Division" should appear in the registry

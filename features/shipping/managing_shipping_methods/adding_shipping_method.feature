@managing_shipping_method
Feature: Adding a new shipping method
    In order to deliver goods in different ways
    As an Administrator
    I want to add a new shipping method to the registry

    Background:
        Given the store operates on a single channel in "France"
        And there is a zone "EU" containing all members of the European Union
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a new shipping method with flat rate per shipment
        Given I want to create a new shipping method
        When I specify its code as "FED_EX_CARRIER"
        And I name it "FedEx Carrier" in "English (United States)"
        And I define it for the "European Union" zone
        And I choose "Flat rate per shipment" calculator
        And I specify its amount as 50
        And I add it
        Then I should be notified that it has been successfully created
        And the shipment method "FedEx Carrier" should appear in the registry

    @ui @javascript
    Scenario: Adding a new shipping method with flat rate per unit
        Given I want to create a new shipping method
        When I specify its code as "FED_EX_CARRIER"
        And I name it "FedEx Carrier" in "English (United States)"
        And I define it for the "European Union" zone
        And I choose "Flat rate per unit" calculator
        And I specify its amount as 20
        And I add it
        Then I should be notified that it has been successfully created
        And the shipment method "FedEx Carrier" should appear in the registry

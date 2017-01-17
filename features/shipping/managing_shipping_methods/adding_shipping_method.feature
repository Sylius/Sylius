@managing_shipping_methods
Feature: Adding a new shipping method
    In order to deliver goods in different ways
    As an Administrator
    I want to add a new shipping method to the registry

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store is available in "English (United States)"
        And the store has a zone "United States" with code "US"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a new shipping method with flat rate per shipment
        When I want to create a new shipping method
        And I specify its code as "FED_EX_CARRIER"
        And I specify its position as 0
        And I name it "FedEx Carrier" in "English (United States)"
        And I define it for the "United States" zone
        And I choose "Flat rate per shipment" calculator
        And I specify its amount as 50 for "Web-US" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the shipping method "FedEx Carrier" should appear in the registry

    @ui @javascript
    Scenario: Adding a new shipping method with description and flat rate per shipment
        When I want to create a new shipping method
        And I specify its code as "FED_EX_CARRIER"
        And I name it "FedEx Carrier" in "English (United States)"
        And I describe it as "FedEx Carrier shipping method for United States" in "English (United States)"
        And I define it for the "United States" zone
        And I choose "Flat rate per shipment" calculator
        And I specify its amount as 50 for "Web-US" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the shipping method "FedEx Carrier" should appear in the registry

    @ui @javascript
    Scenario: Adding a new shipping method with flat rate per unit
        When I want to create a new shipping method
        And I specify its code as "FED_EX_CARRIER"
        And I name it "FedEx Carrier" in "English (United States)"
        And I define it for the "United States" zone
        And I choose "Flat rate per unit" calculator
        And I specify its amount as 20 for "Web-US" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the shipping method "FedEx Carrier" should appear in the registry

    @ui @javascript
    Scenario: Adding a new shipping method for channel
        When I want to create a new shipping method
        And I specify its code as "FED_EX_CARRIER"
        And I name it "FedEx Carrier" in "English (United States)"
        And I describe it as "FedEx Carrier shipping method for United States" in "English (United States)"
        And I define it for the "United States" zone
        And I make it available in channel "Web-US"
        And I choose "Flat rate per unit" calculator
        And I specify its amount as 20 for "Web-US" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the shipping method "FedEx Carrier" should appear in the registry
        And the shipping method "FedEx Carrier" should be available in channel "Web-US"

    @ui @javascript
    Scenario: Adding a new shipping method with flat rate per shipment specified for different channels
        Given the store operates on another channel named "Web-GB" in "GBP" currency
        When I want to create a new shipping method
        And I specify its code as "FED_EX_CARRIER"
        And I name it "FedEx Carrier" in "English (United States)"
        And I define it for the "United States" zone
        And I choose "Flat rate per shipment" calculator
        And I specify its amount as 50 for "Web-US" channel
        And I specify its amount as 40 for "Web-GB" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the shipping method "FedEx Carrier" should appear in the registry

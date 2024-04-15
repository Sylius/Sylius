@managing_shipping_methods
Feature: Adding a new shipping method with rule
    In order to adhere to the shipping provider's limitations on goods
    As an Administrator
    I want to add a new shipping method with a rule to the registry

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store is available in "English (United States)"
        And the store has a zone "United States" with code "US"
        And I am logged in as an administrator

    @ui @mink:chromedriver @api
    Scenario: Adding a new shipping method with total weight greater than or equal rule
        When I want to create a new shipping method
        And I specify its code as "FED_EX_CARRIER"
        And I specify its position as 0
        And I name it "FedEx Carrier" in "English (United States)"
        And I define it for the zone named "United States"
        And I choose "Flat rate per shipment" calculator
        And I specify its amount as 50 for "Web-US" channel
        And I add the "Total weight greater than or equal" rule configured with "20"
        And I add it
        Then I should be notified that it has been successfully created
        And the shipping method "FedEx Carrier" should appear in the registry

    @ui @mink:chromedriver @api
    Scenario: Adding a new shipping method with total weight less than or equal rule
        When I want to create a new shipping method
        And I specify its code as "FED_EX_CARRIER"
        And I specify its position as 0
        And I name it "FedEx Carrier" in "English (United States)"
        And I define it for the zone named "United States"
        And I choose "Flat rate per shipment" calculator
        And I specify its amount as 50 for "Web-US" channel
        And I add the "Total weight less than or equal" rule configured with "20"
        And I add it
        Then I should be notified that it has been successfully created
        And the shipping method "FedEx Carrier" should appear in the registry

    @ui @mink:chromedriver @api
    Scenario: Adding a new shipping method with order total greater than or equal rule
        When I want to create a new shipping method
        And I specify its code as "FED_EX_CARRIER"
        And I specify its position as 0
        And I name it "FedEx Carrier" in "English (United States)"
        And I define it for the zone named "United States"
        And I choose "Flat rate per shipment" calculator
        And I specify its amount as 50 for "Web-US" channel
        And I add the "Items total greater than or equal" rule configured with $200 for "Web-US" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the shipping method "FedEx Carrier" should appear in the registry

    @ui @mink:chromedriver @api
    Scenario: Adding a new shipping method with order total less than or equal rule
        When I want to create a new shipping method
        And I specify its code as "FED_EX_CARRIER"
        And I specify its position as 0
        And I name it "FedEx Carrier" in "English (United States)"
        And I define it for the zone named "United States"
        And I choose "Flat rate per shipment" calculator
        And I specify its amount as 50 for "Web-US" channel
        And I add the "Items total less than or equal" rule configured with $200 for "Web-US" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the shipping method "FedEx Carrier" should appear in the registry

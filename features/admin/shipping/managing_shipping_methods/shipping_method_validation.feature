@managing_shipping_methods
Feature: Shipping method validation
    In order to avoid making mistakes when managing a shipping method
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store has a zone "United States" with code "US"
        And the store is available in "English (United States)"
        And I am logged in as an administrator


    @no-ui @api
    Scenario: Trying to add shipping method translation in unexisting locale
        When I want to create a new shipping method
        And I specify its code as "UPS"
        And I name it "Transporteur UPS" in "French (France)"
        And I try to save my changes
        Then I should be notified that the locale is not available

    @ui @api
    Scenario: Trying to add a new shipping method without specifying its code
        When I want to create a new shipping method
        And I name it "FedEx Carrier" in "English (United States)"
        But I do not specify its code
        And I try to add it
        Then I should be notified that code is required
        And shipping method with name "FedEx Carrier" should not be added

    @ui @api
    Scenario: Trying to add a new shipping method with a too long code
        When I want to create a new shipping method
        And I name it "FedEx Carrier" in "English (United States)"
        And I specify a too long code
        And I try to add it
        Then I should be notified that code is too long

    @ui @api
    Scenario: Trying to add a new shipping method without specifying its name
        When I want to create a new shipping method
        And I specify its code as "FED_EX"
        But I do not name it
        And I try to add it
        Then I should be notified that name is required
        And shipping method with code "FED_EX" should not be added

    @ui @api
    Scenario: Trying to add a new shipping method without specifying its zone
        Given the store does not have any zones defined
        When I want to create a new shipping method
        And I name it "FedEx Carrier" in "English (United States)"
        But I do not specify its zone
        And I try to add it
        Then I should be notified that zone has to be selected
        And shipping method with name "Food and Beverage Tax Rates" should not be added

    @ui @api
    Scenario: Trying to remove name from existing shipping method
        Given the store allows shipping with "UPS Ground"
        When I want to modify this shipping method
        And I remove its name from "English (United States)" translation
        And I try to save my changes
        Then I should be notified that name is required
        And this shipping method should still be named "UPS Ground"

    @ui @api
    Scenario: Trying to remove zone from existing shipping method
        Given the store allows shipping with "UPS Ground"
        When I want to modify this shipping method
        And I remove its zone
        And I try to save my changes
        Then I should be notified that the zone is required

    @ui @mink:chromedriver @api
    Scenario: Adding a new shipping method with order total greater than or equal rule that contains invalid data
        When I want to create a new shipping method
        And I specify its code as "FED_EX_CARRIER"
        And I specify its position as 0
        And I name it "FedEx Carrier" in "English (United States)"
        And I define it for the zone named "United States"
        And I choose "Flat rate per shipment" calculator
        And I specify its amount as 50 for "Web-US" channel
        And I add the "Total weight greater than or equal" rule configured with invalid data
        And I add it
        Then I should be notified that the weight rule has an invalid configuration
        And the shipping method "FedEx Carrier" should not appear in the registry

    @ui @mink:chromedriver @api
    Scenario: Adding a new shipping method with order total less than or equal rule that contains invalid data
        When I want to create a new shipping method
        And I specify its code as "FED_EX_CARRIER"
        And I specify its position as 0
        And I name it "FedEx Carrier" in "English (United States)"
        And I define it for the zone named "United States"
        And I choose "Flat rate per shipment" calculator
        And I specify its amount as 50 for "Web-US" channel
        And I add the "Items total less than or equal" rule configured with invalid data for "Web-US" channel
        And I add it
        Then I should be notified that the amount rule has an invalid configuration in "Web-US" channel
        And the shipping method "FedEx Carrier" should not appear in the registry

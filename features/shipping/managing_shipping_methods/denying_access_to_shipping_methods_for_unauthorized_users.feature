@managing_shipping_methods
Feature: Denying access to shipping methods for unauthorized users
    In order to denies access for unauthorized users
    As an Visitor
    I don't want to access to manage shipping methods

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store is available in "English (United States)"
        And the store has a zone "United States" with code "US"
        And the store allows shipping with "UPS Carrier" identified by "UPS_CARRIER"

    @api
    Scenario: Trying to add a new shipping method as a unauthorized user
        When I try to create a new shipping method
        And I specify its code as "FED_EX_CARRIER"
        And I specify its position as 0
        And I name it "FedEx Carrier" in "English (United States)"
        And I define it for the zone named "United States"
        And I choose "Flat rate per shipment" calculator
        And I specify its amount as 50 for "Web-US" channel
        And I try to add it
        Then I should be notified that my access has been denied

    @api
    Scenario: Trying to rename the shipping method
        Given I try to modify a shipping method "UPS Carrier"
        When I rename it to "UPS Transport" in "English (United States)"
        And I try to save my changes
        Then I should be notified that my access has been denied

    @api
    Scenario: Trying to browse shipping methods
        When I try to browse shipping methods
        Then I should be notified that my access has been denied

    @api
    Scenario: Trying to show shipping method
        When I try to show "UPS Carrier" shipping method
        Then I should be notified that my access has been denied

    @api
    Scenario: Trying to archive a shipping method
        When I try to archive the "UPS Carrier" shipping method
        Then I should be notified that my access has been denied

    @api
    Scenario: Trying to restore a shipping method
        Given the shipping method "UPS Carrier" is archival
        When I try to restore the "UPS Carrier" shipping method
        Then I should be notified that my access has been denied

    @api
    Scenario: Trying to delete shipping method
        When I try to delete shipping method "UPS Carrier"
        Then I should be notified that my access has been denied

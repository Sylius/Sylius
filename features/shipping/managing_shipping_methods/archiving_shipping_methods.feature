@managing_shipping_methods
Feature: Archiving obsolete shipping methods
    In order to hide no longer available shipping methods from the list and customers' use
    As an Administrator
    I want to archive shipping methods

    Background:
        Given the store operates on a single channel in "United States"
        And the store allows shipping with "UPS Carrier" and "FedEx Carrier"
        And I am logged in as an administrator

    @ui
    Scenario: Archiving a shipping method
        Given I am browsing shipping methods
        When I archive the "UPS Carrier" shipping method
        Then the only shipping method on the list should be "FedEx Carrier"

    @domain
    Scenario: Archiving a shipping method does not remove it from the database
        When I archive the "UPS Carrier" shipping method
        Then the shipping method "UPS Carrier" should still exist in the registry

    @ui
    Scenario: Seeing only archived shipping methods
        Given the shipping method "UPS Carrier" is archival
        And I am browsing shipping methods
        When I filter archival shipping methods
        Then the only shipping method on the list should be "UPS Carrier"

    @ui
    Scenario: Restoring an archival shipping method
        Given the shipping method "UPS Carrier" is archival
        And I am browsing archival shipping methods
        When I restore the "UPS Carrier" shipping method
        Then I should be viewing non archival shipping methods
        And I should see 2 shipping methods on the list

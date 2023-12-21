@managing_shipping_methods
Feature: Deleting multiple shipping methods
    In order to remove test, obsolete or incorrect shipping methods in an efficient way
    As an Administrator
    I want to be able to delete multiple shipping methods at once

    Background:
        Given the store operates on a single channel in "United States"
        And the store allows shipping with "UPS", "FedEx" and "DHL"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Deleting multiple shipping methods at once
        When I browse shipping methods
        And I check the "UPS" shipping method
        And I check also the "FedEx" shipping method
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see a single shipping method in the list
        And I should see the shipping method "DHL" in the list

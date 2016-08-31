@managing_shipping_methods
Feature: Deleting a shipping method
    In order to remove test, obsolete or incorrect shipping methods
    As an Administrator
    I want to be able to delete a shipping method

    Background:
        Given the store operates on a single channel in "United States"
        And the store allows shipping with "UPS Ground"
        And I am logged in as an administrator

    @ui
    Scenario: Deleted shipping method should disappear from the registry
        When I delete shipping method "UPS Ground"
        Then I should be notified that it has been successfully deleted
        Then this shipping method should no longer exist in the registry

@managing_shipping_methods
Feature: Deleting a shipping method
    In order to remove test, obsolete or incorrect shipping methods
    As an Administrator
    I want to be able to delete a shipping method

    Background:
        Given the store is available in "English (United States)"
        And the store has a base currency "Euro"
        And there is a zone "EU" containing all members of the European Union
        And the store allows shipping with "UPS Ground"
        And I am logged in as an administrator

    @ui
    Scenario: Deleted shipping method should disappear from the registry
        When I delete shipping method "UPS Ground"
        Then I should be notified that it has been successfully deleted
        Then this shipping method should no longer exist in the registry

@managing_shipping_categories
Feature: Deleting a shipping category
    In order to remove test, obsolete or incorrect shipping categories
    As an Administrator
    I want to be able to delete a shipping category

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Standard" shipping category
        And I am logged in as an administrator

    @ui
    Scenario: Deleted shipping category should disappear from the registry
        When I delete shipping category "Standard"
        Then I should be notified that it has been successfully deleted
        Then this shipping category should no longer exist in the registry

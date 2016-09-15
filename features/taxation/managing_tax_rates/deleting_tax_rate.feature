@managing_tax_rates
Feature: Deleting a tax rate
    In order to remove test, obsolete or incorrect tax rates
    As an Administrator
    I want to be able to delete a tax rate

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a tax category "Sports gear"
        And the store has "United States Sales Tax" tax rate of 20% for "Sports gear" within the "US" zone
        And I am logged in as an administrator

    @ui
    Scenario: Deleted tax rate should disappear from the registry
        When I delete tax rate "United States Sales Tax"
        Then I should be notified that it has been successfully deleted
        Then this tax rate should no longer exist in the registry

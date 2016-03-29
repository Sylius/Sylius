@managing_tax_rates
Feature: Deleting a tax rate
    In order to remove test, obsolete or incorrect tax rates
    As an Administrator
    I want to be able to delete a tax rate

    Background:
        Given there is a zone "EU" containing all members of the European Union
        And the store has a tax category "Sports gear"
        And the store has "European Union Sales Tax" tax rate of 20% for "Sports gear" within "EU" zone
        And I am logged in as an administrator

    @ui
    Scenario: Deleted tax category should disappear from the registry
        When I delete tax rate "European Union Sales Tax"
        Then I should be notified that it has been successfully deleted
        Then this tax rate should no longer exist in the registry

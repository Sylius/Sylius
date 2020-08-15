@managing_tax_rates
Feature: Deleting multiple tax rates
    In order to remove test, obsolete or incorrect tax rates in an efficient way
    As an Administrator
    I want to be able to delete multiple tax rates at once

    Background:
        Given there is a zone "The Rest of the World" containing all other countries
        And the store has "VAT" tax rate of 23% for "Alcohol" for the rest of the world
        And the store has "Low VAT" tax rate of 8% for "Books" for the rest of the world
        And the store has "High VAT" tax rate of 40% for "Food" for the rest of the world
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Deleting multiple tax rates at once
        When I browse tax rates
        And I check the "Low VAT" tax rate
        And I check also the "High VAT" tax rate
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see a single tax rate in the list
        And I should see the tax rate "VAT" in the list

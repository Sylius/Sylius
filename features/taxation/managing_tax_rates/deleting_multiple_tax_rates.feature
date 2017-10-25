@managing_tax_categories
Feature: Deleting multiple tax rates
    In order to remove test, obsolete or incorrect tax rates
    As an Administrator
    I want to be able to delete multiple tax rates

    Background:
        Given the store has "VAT" tax rate of 23% for "Alcohol" for the rest of the world
        And the store has "Low VAT" tax rate of 8% for "Books" for the rest of the world
        And the store has "High VAT" tax rate of 40% for "Food" for the rest of the world
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Deleting multiple tax rates
        When I browse tax rates
        And I check the "VAT" tax rate
        And I check also the "Low VAT" tax rate
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see a single tax rate in the list
        And I should see the tax rate "High VAT" in the list

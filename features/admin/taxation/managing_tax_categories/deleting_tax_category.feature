@managing_tax_categories
Feature: Deleting a tax category
    In order to remove test, obsolete or incorrect tax categories
    As an Administrator
    I want to be able to delete a tax category

    Background:
        Given the store has a tax category "Alcohol" with a code "alcohol"
        And I am logged in as an administrator

    @ui @api
    Scenario: Deleted tax category should disappear from the registry
        When I delete tax category "Alcohol"
        Then I should be notified that it has been successfully deleted
        And this tax category should no longer exist in the registry

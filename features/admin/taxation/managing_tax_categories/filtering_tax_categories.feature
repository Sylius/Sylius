@managing_tax_categories
Feature: Filtering tax categories
    In order to quickly find the tax category I need
    As an Administrator
    I want to filter available tax categories

    Background:
        Given the store has a tax category "Alcohol" with a code "alcohol"
        And the store has a tax category "Food" with a code "food"
        And the store has a tax category "Seafood" with a code "seafood"
        And I am logged in as an administrator
        And I am browsing tax categories

    @api @ui
    Scenario: Filtering tax categories by name
        When I search by "ood" name
        Then I should see 2 tax categories in the list
        But I should not see the tax category "Alcohol"

    @api @ui
    Scenario: Filtering tax categories by code
        When I search by "alcohol" code
        Then I should see a single tax category in the list
        And I should see the tax category "Alcohol"

@managing_tax_categories
Feature: Adding a new tax category
    In order to apply different taxes to various products
    As an Administrator
    I want to add a new tax category to the registry

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Adding a new tax category
        Given I want to create a new tax category
        When I specify its code as "food_and_beverage"
        And I name it "Food and Beverage"
        And I add it
        Then I should be notified that it has been successfully created
        And the tax category "Food and Beverage" should appear in the registry

    @ui
    Scenario: Adding a new tax category with a description
        Given I want to create a new tax category
        When I specify its code as "food_and_beverage"
        And I name it "Food and Beverage"
        And I describe it as "Food and all alcoholic drinks."
        And I add it
        Then I should be notified that it has been successfully created
        And the tax category "Food and Beverage" should appear in the registry

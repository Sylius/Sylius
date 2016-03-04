@taxation
Feature: Adding tax category
    In order to apply different taxes to various products
    As an Administrator
    I want to add new tax category to the registry

    Background:
        Given I am logged in as administrator

    @todo
    Scenario: Adding new tax category
        Given I want to create new tax category
        When I specify its code as "food_and_beverage"
        And I name it "Food and Beverage"
        And I add it
        Then I should be notified about success
        And this tax category should appear in the registry

    @todo
    Scenario: Adding new tax category with a description
        Given I want to create new tax category
        When I specify its code as "food_and_beverage"
        And I name it "Food and Beverage"
        And I describe it as "Food and all alcoholic drinks."
        And I add it
        Then I should be notified about success
        And this tax category should appear in the registry

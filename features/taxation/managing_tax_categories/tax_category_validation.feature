@taxation
Feature: Tax category validation
    In order to avoid making mistakes when managing tax category
    As an Administrator
    I want to be prevented from adding it without code or name

    Background:
        Given I am logged in as administrator

    @todo
    Scenario: Trying to add new tax category without specifying code
        Given I want to create new tax category
        When I name it "Food and Beverage"
        But I do not specify its code
        And I try to add it
        Then I should be notified that code is required
        And tax category named "Food and Beverage" should not be added

    @todo
    Scenario: Trying to add new tax category without specifying name
        Given I want to create new tax category
        When I specify its code as "food_and_beverage"
        But I do not name it
        And I try to add it
        Then I should be notified that name is required
        And tax category with code "food_and_beverage" should not be added

    @todo
    Scenario: Trying to remove name from existing tax category
        Given the store has "Alcoholic Drinks" tax category with code "alcohol"
        And I want to modify this tax category
        When I remove its name
        And I try to save my changes
        Then I should be notified that name is required
        And this tax category should still be named "Alcoholic Drinks"

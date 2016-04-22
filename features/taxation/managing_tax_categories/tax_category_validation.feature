@managing_tax_categories
Feature: Tax category validation
    In order to avoid making mistakes when managing a tax category
    As an Administrator
    I want to be prevented from adding it without specifying its code or name

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Trying to add a new tax category without specifying its code
        Given I want to create a new tax category
        When I name it "Food and Beverage"
        But I do not specify its code
        And I try to add it
        Then I should be notified that code is required
        And tax category with name "Food and Beverage" should not be added

    @ui
    Scenario: Trying to add a new tax category without specifying its name
        Given I want to create a new tax category
        When I specify its code as "food_and_beverage"
        But I do not name it
        And I try to add it
        Then I should be notified that name is required
        And tax category with code "food_and_beverage" should not be added

    @ui
    Scenario: Trying to remove name from existing tax category
        Given the store has a tax category "Alcoholic Drinks" with a code "alcohol"
        And I want to modify this tax category
        When I remove its name
        And I try to save my changes
        Then I should be notified that name is required
        And this tax category should still be named "Alcoholic Drinks"

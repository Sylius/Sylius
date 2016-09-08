@managing_products
Feature: Editing a product
    In order to change product details
    As an Administrator
    I want to be able to edit a product

    Background:
        Given the store is available in "English (United States)"
        And the store has a product "Dice Brewing"
        And I am logged in as an administrator

    @ui
    Scenario: Disabling inventory for simple product
        Given the "Dice Brewing" product is tracked by the inventory
        And I want to modify the "Dice Brewing" product
        When I disable its tracking
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should not be tracked

    @ui
    Scenario: Enabling inventory for simple product
        Given I want to modify the "Dice Brewing" product
        When I enable its tracking
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should be tracked

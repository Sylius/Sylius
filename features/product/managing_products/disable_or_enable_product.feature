@managing_products
Feature: Toggle the product
    In order to stop or resume the sale of a product
    As an Administrator
    I want to toggle the product

    Background:
        Given the store has a product "Dice Brewing"
        And I am logged in as an administrator

    @ui
    Scenario: Disabling a simple product
        Given the "Dice Brewing" product is enabled
        And I want to modify the "Dice Brewing" product
        When I disable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should be disabled along with its variant

    @ui
    Scenario: Enabling a simple product
        Given the "Dice Brewing" product is disabled
        And I want to modify the "Dice Brewing" product
        When I enable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should be enabled along with its variant

@managing_products
Feature: Toggling the simple product
    In order to stop or resume the sale of a product
    As an Administrator
    I want to toggle the product

    Background:
        Given the store has a product "Dice Brewing"
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Disabling a simple product
        Given the "Dice Brewing" product is enabled
        When I want to modify the "Dice Brewing" product
        And I disable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should be disabled along with its variant

    @ui @no-api
    Scenario: Enabling a simple product
        Given the "Dice Brewing" product is disabled
        When I want to modify the "Dice Brewing" product
        And I enable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should be enabled along with its variant

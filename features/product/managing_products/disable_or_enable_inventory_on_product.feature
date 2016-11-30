@managing_products
Feature: Toggle the inventory tracking
    In order to have the inventory tracked in my shop
    As an Administrator
    I want to toggle the inventory tracking

    Background:
        Given the store has a product "Dice Brewing"
        And I am logged in as an administrator

    @ui
    Scenario: Disabling inventory for a simple product
        Given the "Dice Brewing" product is tracked by the inventory
        And I want to modify the "Dice Brewing" product
        When I disable its inventory tracking
        And I save my changes
        Then I should be notified that it has been successfully edited
        And inventory of this product should not be tracked

    @ui
    Scenario: Enabling inventory for a simple product
        Given I want to modify the "Dice Brewing" product
        When I enable its inventory tracking
        And I save my changes
        Then I should be notified that it has been successfully edited
        And inventory of this product should be tracked

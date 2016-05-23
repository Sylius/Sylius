@managing_products
Feature: Editing a product
    In order to change product details
    As an Administrator
    I want to be able to edit a product

    Background:
        Given the store is available in "English (United States)"
        And the store has a base currency "US Dollar"
        And the store has a product "Dice Brewing"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing disabled code field when editing product
        When I want to modify the "Dice Brewing" product
        Then the code field should be disabled

    @ui
    Scenario: Renaming a simple product
        Given I want to modify the "Dice Brewing" product
        When I rename it to "7 Wonders" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product name should be "7 Wonders"

    @ui
    Scenario: Changing a simple product price
        Given I want to modify the "Dice Brewing" product
        When I change its price to "$15.00"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product price should be "$15.00"

    @ui
    Scenario: Renaming a configurable product
        Given the store has a "Wyborowa Vodka" configurable product
        And I want to modify this product
        When I rename it to "Sobieski Vodka" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product name should be "Sobieski Vodka"

    @ui
    Scenario: Renaming a configurable product with option
        Given the store has a "Wyborowa Vodka" configurable product
        And the store has a product option "Bottle size" with a code "bottle_size"
        And this product has this product option
        And I want to modify this product
        When I rename it to "Sobieski Vodka" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product name should be "Sobieski Vodka"

    @ui
    Scenario: Changing options of configurable product without any variant defined
        Given the store has a "Marvel's T-Shirt" configurable product
        And the store has a product option "T-Shirt size" with a code "t_shirt_size"
        And the store has a product option "T-Shirt color" with a code "t_shirt_color"
        And this product has a "T-Shirt size" option
        And I want to modify this product
        When I add the "T-Shirt color" option to it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have a "T-Shirt color" option

    @ui
    Scenario: Seeing disabled option field when editing product
        Given the store has a "Marvel's T-Shirt" configurable product
        And the store has a product option "T-Shirt size" with a code "t_shirt_size"
        And this product has this product option
        And the product "Marvel's T-Shirt" has "Iron Man T-Shirt" variant priced at "€40.00"
        When I want to modify the "Dice Brewing" product
        Then the option field should be disabled

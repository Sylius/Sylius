@managing_products
Feature: Editing a product
    In order to change product details
    As an Administrator
    I want to be able to edit a product

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Dice Brewing"
        And I am logged in as an administrator

    @ui @api
    Scenario: Being unable to change code of an existing product
        When I want to modify the "Dice Brewing" product
        Then I should not be able to edit its code

    @ui @no-api
    Scenario: Renaming a simple product
        When I want to modify the "Dice Brewing" product
        And I rename it to "7 Wonders" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product name should be "7 Wonders"

    @ui @no-api
    Scenario: Renaming a simple product does not change its variant name
        Given this product only variant was renamed to "Dice Brewing: The Game"
        When I want to modify this product
        And I rename it to "7 Wonders" in "English (United States)"
        And I save my changes
        And I want to view all variants of this product
        Then the first variant in the list should have name "Dice Brewing: The Game"

    @ui @no-api
    Scenario: Changing a simple product price
        When I want to modify the "Dice Brewing" product
        And I change its price to $15.00 for "United States" channel
        And I save my changes
        Then I should be notified that it has been successfully edited
        And it should be priced at $15.00 for channel "United States"

    @ui @no-api
    Scenario: Changing a simple product price
        When I want to modify the "Dice Brewing" product
        And I change its price to $7.50 for "United States" channel
        And I change its original price to "$15.00" for "United States" channel
        And I save my changes
        Then I should be notified that it has been successfully edited
        And it should be priced at $7.50 for channel "United States"
        And its original price should be "$15.00" for channel "United States"

    @ui @api
    Scenario: Renaming a configurable product
        Given the store has a "Wyborowa Vodka" configurable product
        When I want to modify this product
        And I rename it to "Sobieski Vodka" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product name should be "Sobieski Vodka"

    @ui @api
    Scenario: Renaming a configurable product with option
        Given the store has a "Wyborowa Vodka" configurable product
        And the store has a product option "Bottle size" with a code "bottle_size"
        And this product has this product option
        When I want to modify this product
        And I rename it to "Sobieski Vodka" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product name should be "Sobieski Vodka"

    @ui @javascript @api
    Scenario: Changing options of configurable product without any variant defined
        Given the store has a "Marvel's T-Shirt" configurable product
        And the store has a product option "T-Shirt size" with a code "t_shirt_size"
        And the store has a product option "T-Shirt color" with a code "t_shirt_color"
        And this product has a "T-Shirt size" option
        When I want to modify this product
        And I add the "T-Shirt color" option to it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have a "T-Shirt color" option

    @ui @api
    Scenario: Being unable to change options of an existing product
        Given the store has a "Marvel's T-Shirt" configurable product
        And the store has a product option "T-Shirt size" with a code "t_shirt_size"
        And this product has this product option
        And the store has also a product option "T-Shirt color"
        And the product "Marvel's T-Shirt" has "Iron Man T-Shirt" variant priced at "$40.00"
        When I want to modify the "Marvel's T-Shirt" product
        Then I should not be able to edit its options

    @ui @api
    Scenario: Enabling product in channel when all its variants already have specified price in this channel
        Given the store operates on another channel named "Mobile"
        And the store has a "7 Wonders" configurable product
        And this product has "7 Wonders: Cities" variant priced at "$30.00" in "United States" channel
        And this variant is also priced at "$25.00" in "Mobile" channel
        And this product has "7 Wonders: Leaders" variant priced at "$20.00" in "United States" channel
        And this variant is also priced at "$20.00" in "Mobile" channel
        When I want to modify the "7 Wonders" product
        And I assign it to channel "Mobile"
        And I save my changes
        Then I should be notified that it has been successfully edited

@managing_products
Feature: Adding a product with only original price
    In order to prepare product in all channel
    As an Administrator
    I want to be able to create product without price in disabled channel

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Adding a new simple product without price
        When I want to create a new simple product
        And I specify its code as "BOARD_DICE_BREWING"
        And I name it "Dice Brewing" in "English (United States)"
        And I set its slug to "games/Dice-brewing" in "English (United States)"
        And I set its original price to "$100.00" for "United States" channel
        And I add it
        Then I should be notified that it has been successfully created
        And I should have original price equal to "$100.00" in "United States" channel

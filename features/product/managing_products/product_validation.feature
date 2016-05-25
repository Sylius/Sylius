@managing_products
Feature: Products validation
    In order to avoid making mistakes when managing a product
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store is available in "English (United States)"
        And the store has a base currency "US Dollar"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new simple product without specifying its code
        Given I want to create a new simple product
        When I name it "Dice Brewing" in "English (United States)"
        And I set its price to "$10.00"
        And I try to add it
        Then I should be notified that code is required
        And product with name "Dice Brewing" should not be added

    @ui
    Scenario: Adding a new simple product without specifying its name
        Given I want to create a new simple product
        When I specify its code as "BOARD_DICE_BREWING"
        And I set its price to "$10.00"
        And I try to add it
        Then I should be notified that name is required
        And product with code "BOARD_DICE_BREWING" should not be added

    @ui
    Scenario: Adding a new simple product without specifying its price
        Given I want to create a new simple product
        When I specify its code as "BOARD_DICE_BREWING"
        And I name it "Dice Brewing" in "English (United States)"
        And I try to add it
        Then I should be notified that price is required
        And product with code "BOARD_DICE_BREWING" should not be added

    @ui
    Scenario: Adding a new configurable product without specifying its code
        Given I want to create a new configurable product
        When I name it "Dice Brewing" in "English (United States)"
        And I try to add it
        Then I should be notified that code is required
        And product with name "Dice Brewing" should not be added

    @ui
    Scenario: Adding a new configurable product without specifying its name
        Given I want to create a new configurable product
        When I specify its code as "BOARD_DICE_BREWING"
        And I try to add it
        Then I should be notified that name is required
        And product with code "BOARD_DICE_BREWING" should not be added

    @ui
    Scenario: Trying to remove name from existing simple product
        Given the store has a "Dice Brewing" product
        And I want to modify this product
        When I remove its name from "English (United States)" translation
        And I try to save my changes
        Then I should be notified that name is required
        And this product should still be named "Dice Brewing"

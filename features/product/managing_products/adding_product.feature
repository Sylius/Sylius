@managing_products
Feature: Adding a new product
    In order to extend my merchandise
    As an Administrator
    I want to add a new product to the shop

    Background:
        Given the store is available in "English (United States)"
        And the store has a base currency "US Dollar"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new simple product
        Given I want to create a new simple product
        When I specify its code as "BOARD_DICE_BREWING"
        And I name it "Dice Brewing" in "English (United States)"
        And I set its price to "$10.00"
        And I add it
        Then I should be notified that it has been successfully created
        And the product "Dice Brewing" should appear in the shop

    @ui
    Scenario: Adding a new configurable product
        Given the store has a product option "Bottle size" with a code "bottle_size"
        And this product option has the "0.7" option value with code "bottle_size_medium"
        And this product option has also the "0.5" option value with code "bottle_size_small"
        And I want to create a new configurable product
        When I specify its code as "WHISKEY_GENTLEMEN"
        And I name it "Gentleman Jack" in "English (United States)"
        And I add the "Bottle size" option to it
        And I add it
        Then I should be notified that it has been successfully created
        And the product "Gentleman Jack" should appear in the shop

    @ui
    Scenario: Adding a new configurable product without options
        Given I want to create a new configurable product
        When I specify its code as "WHISKEY_GENTLEMEN"
        And I name it "Gentleman Jack" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the product "Gentleman Jack" should appear in the shop

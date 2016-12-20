@managing_products
Feature: Products validation
    In order to avoid making mistakes when managing a product
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new simple product without specifying its code
        Given I want to create a new simple product
        When I name it "Dice Brewing" in "English (United States)"
        And I set its price to "$10.00" for "United States" channel
        And I try to add it
        Then I should be notified that code is required
        And product with name "Dice Brewing" should not be added

    @ui
    Scenario: Adding a new simple product with duplicated code among products
        Given the store has a product "7 Wonders" with code "AWESOME_GAME"
        And I want to create a new simple product
        When I specify its code as "AWESOME_GAME"
        And I name it "Dice Brewing" in "English (United States)"
        And I set its price to "$10.00" for "United States" channel
        And I try to add it
        Then I should be notified that code has to be unique
        And product with name "Dice Brewing" should not be added

    @ui
    Scenario: Adding a new simple product with duplicated code among product variants
        Given the store has a product "7 Wonders"
        And this product has "7 Wonders: Cities" variant priced at "$30" identified by "AWESOME_GAME"
        And I want to create a new simple product
        When I specify its code as "AWESOME_GAME"
        And I name it "Dice Brewing" in "English (United States)"
        And I set its price to "$10.00" for "United States" channel
        And I try to add it
        Then I should be notified that simple product code has to be unique
        And product with name "Dice Brewing" should not be added

    @ui
    Scenario: Adding a new simple product without specifying its slug
        Given I want to create a new simple product
        When I specify its code as "BOARD_DICE_BREWING"
        And I name it "Dice Brewing" in "English (United States)"
        And I set its price to "$10.00" for "United States" channel
        And I remove its slug
        And I try to add it
        Then I should be notified that slug is required
        And product with name "Dice Brewing" should not be added

    @ui
    Scenario: Adding a new simple product without specifying its name
        Given I want to create a new simple product
        When I specify its code as "BOARD_DICE_BREWING"
        And I set its price to "$10.00" for "United States" channel
        And I try to add it
        Then I should be notified that name is required
        And product with code "BOARD_DICE_BREWING" should not be added

    @ui
    Scenario: Adding a new simple product without specifying its price for every channel
        Given the store operates on another channel named "Web-GB"
        When I want to create a new simple product
        And I specify its code as "BOARD_DICE_BREWING"
        And I make it available in channel "United States"
        And I make it available in channel "Web-GB"
        And I set its price to "$10.00" for "United States" channel
        And I name it "Dice Brewing" in "English (United States)"
        And I try to add it
        Then I should be notified that price must be defined for every channel
        And product with code "BOARD_DICE_BREWING" should not be added

    @ui
    Scenario: Adding a new configurable product without specifying its code
        Given I want to create a new configurable product
        When I name it "Dice Brewing" in "English (United States)"
        And I try to add it
        Then I should be notified that code is required
        And product with name "Dice Brewing" should not be added

    @ui
    Scenario: Adding a new configurable product with duplicated code
        Given the store has a product "7 Wonders" with code "AWESOME_GAME"
        And I want to create a new configurable product
        When I specify its code as "AWESOME_GAME"
        And I name it "Dice Brewing" in "English (United States)"
        And I try to add it
        Then I should be notified that code has to be unique
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

    @ui
    Scenario: Not seeing validation error for duplicated code if product code has not been changed
        Given the store has a "Dice Brewing" product
        And I want to modify this product
        When I remove its name from "English (United States)" translation
        And I try to save my changes
        Then I should not be notified that simple product code has to be unique
        And this product should still be named "Dice Brewing"

    @ui
    Scenario: Trying to assign new channel to an existing configurable product without specifying its all variant prices for this channel
        Given the store has a "7 Wonders" configurable product
        And this product has "7 Wonders: Cities" variant priced at "$30" in "United States" channel
        And this product has "7 Wonders: Leaders" variant priced at "$20" in "United States" channel
        And the store operates on another channel named "Mobile Channel"
        When I want to modify the "7 Wonders" product
        And I assign it to channel "Mobile Channel"
        And I save my changes
        Then I should be notified that I have to define product variants' prices for newly assigned channels first

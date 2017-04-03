@managing_products
Feature: Products validation
    In order to avoid making mistakes when managing a product
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store operates on a channel named "Web"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And it uses the "English (United States)" locale by default
        And the store has a product "Symfony Mug"
        And the store has a text product attribute "Mug material"
        And this product attribute has set min value as 3 and max value as 30
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new simple product without specifying its code
        Given I want to create a new simple product
        When I name it "Dice Brewing" in "English (United States)"
        And I set its price to "$10.00" for "Web" channel
        And I try to add it
        Then I should be notified that code is required
        And product with name "Dice Brewing" should not be added

    @ui
    Scenario: Adding a new simple product with duplicated code among products
        Given the store has a product "7 Wonders" with code "AWESOME_GAME"
        And I want to create a new simple product
        When I specify its code as "AWESOME_GAME"
        And I name it "Dice Brewing" in "English (United States)"
        And I set its price to "$10.00" for "Web" channel
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
        And I set its price to "$10.00" for "Web" channel
        And I try to add it
        Then I should be notified that simple product code has to be unique
        And product with name "Dice Brewing" should not be added

    @ui
    Scenario: Adding a new simple product without specifying its slug
        Given I want to create a new simple product
        When I specify its code as "BOARD_DICE_BREWING"
        And I name it "Dice Brewing" in "English (United States)"
        And I set its price to "$10.00" for "Web" channel
        And I remove its slug
        And I try to add it
        Then I should be notified that slug is required
        And product with name "Dice Brewing" should not be added

    @ui
    Scenario: Adding a new simple product without specifying its name
        Given I want to create a new simple product
        When I specify its code as "BOARD_DICE_BREWING"
        And I set its price to "$10.00" for "Web" channel
        And I try to add it
        Then I should be notified that name is required
        And product with code "BOARD_DICE_BREWING" should not be added

    @ui
    Scenario: Adding a new simple product without specifying its price for every channel
        Given the store operates on another channel named "Web-GB"
        When I want to create a new simple product
        And I specify its code as "BOARD_DICE_BREWING"
        And I make it available in channel "Web"
        And I make it available in channel "Web-GB"
        And I set its price to "$10.00" for "Web" channel
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
        Then this product should still be named "Dice Brewing"

    @ui
    Scenario: Trying to assign new channel to an existing configurable product without specifying its all variant prices for this channel
        Given the store has a "7 Wonders" configurable product
        And this product has "7 Wonders: Cities" variant priced at "$30"
        And this product has "7 Wonders: Leaders" variant priced at "$20"
        And the store operates on another channel named "Mobile Channel"
        When I want to modify the "7 Wonders" product
        And I assign it to channel "Mobile Channel"
        And I save my changes
        Then I should be notified that I have to define product variants' prices for newly assigned channels first

    @ui
    Scenario: Adding a new simple product with price
        Given the store has a "7 Wonders" configurable product with "7-wonders" slug
        And I want to create a new configurable product
        When I specify its code as "7-WONDERS-BABEL"
        And I name it "7 Wonders Babel" in "English (United States)"
        And I set its slug to "7-wonders" in "English (United States)"
        And I add it
        Then I should be notified that slug has to be unique
        And product with code "7-WONDERS-BABEL" should not be added

    @ui @javascript
    Scenario: Trying to add a new product with a text attribute without specifying its value in default locale
        When I want to create a new simple product
        And I specify its code as "X-18-MUG"
        And I name it "PHP Mug" in "English (United States)"
        And I set its price to "$100.00" for "Web" channel
        And I set its "Mug material" attribute to "Drewno" in "Polish (Poland)"
        But I do not set its "Mug material" attribute in "English (United States)"
        And I add it
        Then I should be notified that I have to define the "Mug material" attribute in "English (United States)"
        And product with code "X-18-MUG" should not be added

    @ui @javascript
    Scenario: Trying to add a new product with a text attribute without specifying its value in additional locale with proper length
        When I want to create a new simple product
        And I specify its code as "X-18-MUG"
        And I name it "PHP Mug" in "English (United States)"
        And I set its price to "$100.00" for "Web" channel
        And I set its "Mug material" attribute to "Dr" in "Polish (Poland)"
        And I set its "Mug material" attribute to "Wood" in "English (United States)"
        And I add it
        Then I should be notified that the "Mug material" attribute in "Polish (Poland)" should be longer than 3
        And product with code "X-18-MUG" should not be added

    @ui @javascript
    Scenario: Trying to add a text attribute in different locales to an existing product without specifying its value in default locale
        When I want to modify the "Symfony Mug" product
        And I set its "Mug material" attribute to "Drewno" in "Polish (Poland)"
        But I do not set its "Mug material" attribute in "English (United States)"
        And I save my changes
        Then I should be notified that I have to define the "Mug material" attribute in "English (United States)"

    @ui @javascript
    Scenario: Trying to add a text attribute in different locales to an existing product without specifying its value in additional locale with proper length
        When I want to modify the "Symfony Mug" product
        And I set its "Mug material" attribute to "Dr" in "Polish (Poland)"
        And I set its "Mug material" attribute to "Wood" in "English (United States)"
        And I save my changes
        Then I should be notified that the "Mug material" attribute in "Polish (Poland)" should be longer than 3

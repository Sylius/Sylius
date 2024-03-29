@managing_catalog_promotions
Feature: Creating a catalog promotion
    In order to set up a catalog promotion for some special occasions
    As an Administrator
    I want to have an option to configure such a promotion

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Clothes" taxonomy
        And the store has a "T-Shirt" configurable product
        And this product belongs to "Clothes"
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And this product has "Kotlin T-Shirt" variant priced at "$40.00"
        And I am logged in as an administrator

    @api @ui
    Scenario: Creating a simple catalog promotion only with code and name
        When I create a new catalog promotion with "winter_sale" code and "Winter sale" name
        Then there should be 1 new catalog promotion on the list
        And it should have "winter_sale" code and "Winter sale" name

    @api @ui @mink:chromedriver
    Scenario: Creating an enabled catalog promotion
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And I add scope that applies on variants "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And I add action that gives "50%" percentage discount
        And I make it available in channel "United States"
        And I enable it
        And I add it
        Then there should be 1 new catalog promotion on the list
        And it should have "winter_sale" code and "Winter sale" name
        And "Winter sale" catalog promotion should apply to "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And it should have "50.00%" discount
        And it should be active
        And "PHP T-Shirt" variant and "Kotlin T-Shirt" variant should be discounted

    @api @ui @mink:chromedriver
    Scenario: Creating a disabled catalog promotion
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And I add scope that applies on variants "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And I add action that gives "50%" percentage discount
        And I disable it
        And I add it
        Then there should be 1 new catalog promotion on the list
        And it should have "winter_sale" code and "Winter sale" name
        And "Winter sale" catalog promotion should apply to "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And it should have "50.00%" discount
        And it should be inactive
        And "PHP T-Shirt" variant and "Kotlin T-Shirt" variant should not be discounted

    @api @ui
    Scenario: Creating a catalog promotion for channel
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I make it available in channel "United States"
        And I add it
        Then there should be 1 new catalog promotion on the list
        And it should have "winter_sale" code and "Winter sale" name
        And the catalog promotion "Winter sale" should be available in channel "United States"

    @api @ui @mink:chromedriver
    Scenario: Creating a catalog promotion with fixed discount action
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I add scope that applies on "Clothes" taxon
        And I add action that gives "$10.00" of fixed discount in the "United States" channel
        And I add it
        Then there should be 1 new catalog promotion on the list
        And it should have "winter_sale" code and "Winter sale" name
        And the "Winter sale" catalog promotion should have "$10.00" of fixed discount in the "United States" channel

    @api @ui @mink:chromedriver
    Scenario: Creating a catalog promotion for taxon
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I add scope that applies on "Clothes" taxon
        And I add action that gives "50%" percentage discount
        And I make it available in channel "United States"
        And I add it
        Then there should be 1 new catalog promotion on the list
        And it should have "winter_sale" code and "Winter sale" name
        And "Winter sale" catalog promotion should apply to all products from "Clothes" taxon
        And the catalog promotion "Winter sale" should be available in channel "United States"

    @api @ui @mink:chromedriver
    Scenario: Creating a catalog promotion for product
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I add scope that applies on "T-Shirt" product
        And I add action that gives "50%" percentage discount
        And I add it
        Then there should be 1 new catalog promotion on the list
        And it should have "winter_sale" code and "Winter sale" name
        And the "Winter sale" catalog promotion should apply to all variants of "T-Shirt" product

    @api @ui @mink:chromedriver
    Scenario: Creating a catalog promotion with start and end date
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I add scope that applies on variants "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And I add action that gives "50%" percentage discount
        And I make it start yesterday and ends tomorrow
        And I add it
        Then I should be notified that catalog promotion has been successfully created
        And there should be 1 new catalog promotion on the list
        And it should have "winter_sale" code and "Winter sale" name
        And "Winter sale" catalog promotion should apply to "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And it should operate between yesterday and tomorrow
        And it should be active

    @api @ui
    Scenario: Creating a simple catalog promotion with code, name and priority
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I set its priority to 10
        And I add it
        Then there should be 1 new catalog promotion on the list
        And it should have "winter_sale" code and "Winter sale" name
        And it should have priority equal to 10

    @api @ui @mink:chromedriver
    Scenario: Adding a new catalog promotion with priorities with others already existing
        Given there is a catalog promotion "PHP stuff promotion" with priority 100 that reduces price by "30%" and applies on "PHP T-Shirt" variant
        And there is a catalog promotion "T-Shirt sale" with priority 200 that reduces price by "10%" and applies on "PHP T-Shirt" variant
        When I create a "Winter sale" catalog promotion with 90 priority that applies on "T-Shirt" product and reduces price by "25%" in "United States" channel
        Then the visitor should see that the "PHP T-Shirt" variant is discounted from "$20.00" to "$9.45" with 3 promotions
        And the visitor should see that the "Kotlin T-Shirt" variant is discounted from "$40.00" to "$30.00" with "Winter sale" promotion

    @ui @javascript
    Scenario: Adding a new catalog promotion of default type with one action
        When I want to create a new catalog promotion
        And I add a new catalog promotion scope
        And I add a new catalog promotion action
        Then I should see the catalog promotion scope configuration form
        And I should see the catalog promotion action configuration form

    @api @ui @mink:chromedriver
    Scenario: Adding a new catalog promotion that applies on variant with exclusive catalog promotion that has higher priority already applied
        Given there is an exclusive catalog promotion "Spring sale" with priority 120 that reduces price by "50%" and applies on "Kotlin T-Shirt" variant
        When I create a "PHP stuff promotion" catalog promotion with 100 priority that applies on "T-Shirt" product and reduces price by "25%" in "United States" channel
        Then the visitor should see that the "Kotlin T-Shirt" variant is discounted from "$40.00" to "$20.00" with "Spring sale" promotion
        And the visitor should see that the "PHP T-Shirt" variant is discounted from "$20.00" to "$15.00" with "PHP stuff promotion" promotion

    @api @ui @mink:chromedriver
    Scenario: Adding a new catalog promotion that applies on product with exclusive catalog promotion that has lower priority already applied
        Given there is an exclusive catalog promotion "Spring sale" with priority 90 that reduces price by "50%" and applies on "Kotlin T-Shirt" variant
        When I create a "PHP stuff promotion" catalog promotion with 100 priority that applies on "T-Shirt" product and reduces price by "25%" in "United States" channel
        Then the visitor should see that the "Kotlin T-Shirt" variant is discounted from "$40.00" to "$20.00" with "Spring sale" promotion
        And the visitor should see that the "PHP T-Shirt" variant is discounted from "$20.00" to "$15.00" with "PHP stuff promotion" promotion

    @api @ui @mink:chromedriver
    Scenario: Adding an exclusive catalog promotion to existing basic catalog promotion with lower priority
        Given there is a catalog promotion "PHP stuff promotion" with priority 100 that reduces price by "50%" and applies on "PHP T-Shirt" variant
        When I create an exclusive "Spring sale" catalog promotion with 120 priority that applies on "T-Shirt" product and reduces price by "25%" in "United States" channel
        Then the visitor should see that the "PHP T-Shirt" variant is discounted from "$20.00" to "$15.00" with "Spring sale" promotion
        And the visitor should see that the "Kotlin T-Shirt" variant is discounted from "$40.00" to "$30.00" with "Spring sale" promotion

    @api @ui @mink:chromedriver
    Scenario: Adding an exclusive catalog promotion to existing basic catalog promotion with higher priority
        Given there is a catalog promotion "PHP stuff promotion" with priority 100 that reduces price by "50%" and applies on "PHP T-Shirt" variant
        When I create an exclusive "Spring sale" catalog promotion with 90 priority that applies on "T-Shirt" product and reduces price by "75%" in "United States" channel
        Then the visitor should see that the "PHP T-Shirt" variant is discounted from "$20.00" to "$5.00" with "Spring sale" promotion
        And the visitor should see that the "Kotlin T-Shirt" variant is discounted from "$40.00" to "$10.00" with "Spring sale" promotion

    @api @ui @mink:chromedriver
    Scenario: Adding an exclusive catalog promotion to existing exclusive catalog promotion with higher priority
        Given there is an exclusive catalog promotion "Spring sale" with priority 120 that reduces price by "50%" and applies on "Kotlin T-Shirt" variant
        When I create an exclusive "PHP stuff promotion" catalog promotion with 90 priority that applies on "T-Shirt" product and reduces price by "75%" in "United States" channel
        Then the visitor should see that the "PHP T-Shirt" variant is discounted from "$20.00" to "$5.00" with "PHP stuff promotion" promotion
        And the visitor should see that the "Kotlin T-Shirt" variant is discounted from "$40.00" to "$20.00" with "Spring sale" promotion

    @api @ui @mink:chromedriver
    Scenario: Adding an exclusive catalog promotion to existing exclusive catalog promotion with lower priority
        Given there is an exclusive catalog promotion "Spring sale" with priority 90 that reduces price by "50%" and applies on "Kotlin T-Shirt" variant
        When I create an exclusive "PHP stuff promotion" catalog promotion with 120 priority that applies on "T-Shirt" product and reduces price by "75%" in "United States" channel
        Then the visitor should see that the "PHP T-Shirt" variant is discounted from "$20.00" to "$5.00" with "PHP stuff promotion" promotion
        And the visitor should see that the "Kotlin T-Shirt" variant is discounted from "$40.00" to "$10.00" with "PHP stuff promotion" promotion

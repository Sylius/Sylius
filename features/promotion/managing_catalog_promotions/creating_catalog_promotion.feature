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

    @api @ui @javascript
    Scenario: Creating an enabled catalog promotion
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And I add scope that applies on variants "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And I add action that gives "50%" percentage discount
        And I make it available in channel "United States"
        And I enable this catalog promotion
        And I add it
        Then there should be 1 new catalog promotion on the list
        And it should have "winter_sale" code and "Winter sale" name
        And "Winter sale" catalog promotion should apply to "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And it should have "50%" discount
        And this catalog promotion should be usable
        And "PHP T-Shirt" variant and "Kotlin T-Shirt" variant should be discounted

    @api @ui @javascript
    Scenario: Creating a disabled catalog promotion
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And I add scope that applies on variants "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And I add action that gives "50%" percentage discount
        And I disable this catalog promotion
        And I add it
        Then there should be 1 new catalog promotion on the list
        And it should have "winter_sale" code and "Winter sale" name
        And "Winter sale" catalog promotion should apply to "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And it should have "50%" discount
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

    @api @ui @javascript
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

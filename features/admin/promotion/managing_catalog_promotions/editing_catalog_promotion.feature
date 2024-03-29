@managing_catalog_promotions
Feature: Editing catalog promotion
    In order to change catalog promotion details
    As an Administrator
    I want to be able to edit a catalog promotion

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Clothes" taxonomy
        And the store has a "T-Shirt" configurable product
        And this product belongs to "Clothes"
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And this product has "Kotlin T-Shirt" variant priced at "$40.00"
        And there is a catalog promotion with "christmas_sale" code and "Christmas sale" name
        And it applies on "PHP T-Shirt" variant
        And it reduces price by "30%"
        And it is enabled
        And I am logged in as an administrator

    @api @ui
    Scenario: Renaming a catalog promotion
        When I rename the "Christmas sale" catalog promotion to "Black Friday"
        Then I should be notified that it has been successfully edited
        And this catalog promotion name should be "Black Friday"

    @api @ui
    Scenario: Changing label and description of catalog promotion
        When I want to modify a catalog promotion "Christmas sale"
        And I specify its label as "Christmas -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And I save my changes
        Then this catalog promotion should be labelled as "Christmas -50%" in "English (United States)" locale
        And this catalog promotion should be described as "This promotion gives a 50% discount on all products" in "English (United States)" locale

    @api @ui
    Scenario: Changing availability in channels for catalog promotion
        Given the catalog promotion "Christmas sale" is available in "United States"
        And the store operates on a channel named "Europe"
        When I want to modify a catalog promotion "Christmas sale"
        And I make it available in channel "Europe"
        And I make it unavailable in channel "United States"
        And I save my changes
        Then this catalog promotion should be available in channel "Europe"
        But this catalog promotion should not be available in channel "United States"

    @api @ui
    Scenario: Being unable to change code of catalog promotion
        When I want to modify a catalog promotion "Christmas sale"
        Then I should not be able to edit its code

    @api @ui @mink:chromedriver
    Scenario: Editing catalog promotion variant scope
        When I edit "Christmas sale" catalog promotion to be applied on "Kotlin T-Shirt" variant
        Then I should be notified that it has been successfully edited
        And this catalog promotion should be applied on "Kotlin T-Shirt" variant
        And this catalog promotion should not be applied on "PHP T-Shirt" variant

    @api @ui @mink:chromedriver
    Scenario: Editing catalog promotion taxon scope
        When I edit "Christmas sale" catalog promotion to be applied on "Clothes" taxon
        Then I should be notified that it has been successfully edited
        And this catalog promotion should be applied on "Clothes" taxon
        And this catalog promotion should not be applied on "Kotlin T-Shirt" variant
        And this catalog promotion should not be applied on "PHP T-Shirt" variant

    @api @ui @mink:chromedriver
    Scenario: Editing catalog promotion product scope
        When I edit "Christmas sale" catalog promotion to be applied on "T-Shirt" product
        Then I should be notified that it has been successfully edited
        And this catalog promotion should be applied on "T-Shirt" product
        And this catalog promotion should not be applied on "PHP T-Shirt" variant

    @api @ui
    Scenario: Editing catalog promotion action
        When I edit "Christmas sale" catalog promotion to have "40%" discount
        Then I should be notified that it has been successfully edited
        And this catalog promotion should have "40.00%" percentage discount

    @api @ui @javascript
    Scenario: Editing catalog promotion action to be a fixed discount
        When I edit "Christmas sale" catalog promotion to have "$10.00" of fixed discount in the "United States" channel
        Then I should be notified that it has been successfully edited
        And this catalog promotion should have "$10.00" of fixed discount in the "United States" channel

    @api @ui
    Scenario: Being unable to edit catalog promotion if it is currently being processed
        Given the catalog promotion "Christmas sale" is currently being processed
        When I try to rename the "Christmas sale" catalog promotion to "Black Friday"
        Then I should not be able to edit it due to wrong state
        And this catalog promotion name should still be "Christmas sale"

    @api @ui
    Scenario: Being unable to change end date to earlier then start date
        Given the catalog promotion "Christmas sale" operates between "2021-12-20" and "2021-12-30"
        When I want to modify a catalog promotion "Christmas sale"
        And I try to change its end date to "2021-12-15"
        And I save my changes
        Then I should get information that the end date cannot be set before start date

    @api @ui @mink:chromedriver
    Scenario: Receiving error message after not filling price for all channels
        Given the store operates on another channel named "Poland"
        When I want to modify a catalog promotion "Christmas sale"
        And I edit it to have "$10.00" of fixed discount in the "United States" channel
        And I make it available in channel "Poland"
        And I save my changes
        Then I should be notified that not all channels are filled

    @api @ui
    Scenario: Receiving error message after not filling percentage value for percentage discount
        When I want to modify a catalog promotion "Christmas sale"
        And I edit it to have empty amount of percentage discount
        And I save my changes
        Then I should be notified that the percentage amount should be a number and cannot be empty

    @api @ui @javascript
    Scenario: Editing catalog promotion action to be a percentage discount and not filling amount
        Given there is a catalog promotion "Winter sale" that reduces price by fixed "$10.00" in the "United States" channel and applies on "T-Shirt" product
        When I want to modify a catalog promotion "Christmas sale"
        And I edit it to have empty amount of percentage discount
        And I save my changes
        Then I should be notified that the percentage amount should be a number and cannot be empty

    @api @ui @javascript
    Scenario: Editing catalog promotion action to be a fixed discount and not filling amount
        When I want to modify a catalog promotion "Christmas sale"
        And I edit it to have empty amount of fixed discount in the "United States" channel
        And I save my changes
        Then I should be notified that the fixed amount should be a number and cannot be empty

@managing_product_variants
Feature: Adding a product variant with only original price
    In order to prepare product variant in all channel
    As an Administrator
    I want to be able to create product variant without price in disabled channel

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And this product is disabled in "United States" channel
        And I am logged in as an administrator

    @api @ui
    Scenario: Adding a new product variant without price
        When I want to create a new variant of this product
        And I specify its code as "VODKA_WYBOROWA_DELUX"
        And I set its original price to "$100.00" for "United States" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the "VODKA_WYBOROWA_DELUX" variant of the "Wyborowa Vodka" product should appear in the store
        And the variant with code "VODKA_WYBOROWA_DELUX" should be originally priced at "$100.00" for channel "United States"

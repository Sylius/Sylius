@managing_product_variants
Feature: Product variant generation access
    In order to avoid making mistakes when generating product variants
    As an Administrator
    I want to be able to access the product variant generation page only for products with configured options

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Yerba Mate" configurable product
        And I am logged in as an administrator

    @no-api @ui
    Scenario: Being unable to go to the generate variants page for a product without options
        When I want to see the list of variants of the "Yerba Mate" product
        Then I should not be able to go to the generate variants page

    @no-api @ui
    Scenario: Being able to go to the generate variants page for a product with options
        Given this product has option "Taste" with values "Orange" and "Melon"
        When I want to see the list of variants of the "Yerba Mate" product
        And I go to generate variants page
        Then I should be on the "Yerba Mate" product generate variants page

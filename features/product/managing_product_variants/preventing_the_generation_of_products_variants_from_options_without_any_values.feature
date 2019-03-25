@managing_product_variants
Feature: Preventing the generation of product variants from options without any values
    In order to prevented from invalid variant generation
    As an Administrator
    I want to not be able to generate variants without any option values

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And this product has an option "Taste" without any values
        And I am logged in as an administrator

    @ui
    Scenario: Trying to generate a product variant for a product without options values
        When I want to generate new variants for this product
        Then It has been impossible to generate
        And I should not be able to generate any variants

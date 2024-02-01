@managing_product_variants
Feature: Preventing the generation of product variants from options without any values
    In order to prevented from invalid variant generation
    As an Administrator
    I want to be prevented from generating of product variants from options without any values

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And this product has an option "Taste" without any values
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Trying to generate a product variant for a product without options values
        When I try to generate new variants for this product
        Then I should be notified that variants cannot be generated from options without any values
        And I should not be able to generate any variants

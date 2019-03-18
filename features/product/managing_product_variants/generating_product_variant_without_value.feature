@managing_product_variants
    Feature: Generating product variants without any values
        In order to sell variants without any values of a single product
        As an Administrator
        I want to generate variants without any values

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And this product has option "Taste" without values
        And I am logged in as an administrator

    @ui
    Scenario: Generating a product variant for product without options values
        When I want to generate new variants for this product
        Then I should be notified that it has been impossible generate
        And I should not see any variants in the list

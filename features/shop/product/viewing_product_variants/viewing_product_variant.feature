@viewing_product_variants
Feature: Viewing product's enabled variants only
    In order to buy only available product variants
    As a Customer
    I want to see only enabled product variants

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store has a "Super Cool T-Shirt" configurable product
        And this product has "Small", "Medium" and "Large" variants
        And the product "Super Cool T-Shirt" has also an "Extra Large" variant
        And the "Extra Large" product variant is disabled

    @api @no-ui
    Scenario: Viewing only enabled variants
        When I view variants
        Then I should see "Small", "Medium" and "Large" variants
        But I should not see "Extra Large" variant

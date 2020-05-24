@viewing_products
Feature: Viewing product's enabled variants only
    In order to buy only available products
    As a Customer
    I want to see only enabled product variants

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store has a "Super Cool T-Shirt" configurable product
        And this product has "Small", "Medium" and "Large" variants
        And the product "Super Cool T-Shirt" has also an "Extra Large" variant
        And the "Extra Large" product variant is disabled

    @ui
    Scenario: Seeing only enabled variants
        When I view product "Super Cool T-Shirt"
        Then I should be able to select between 3 variants
        And I should not be able to select the "Extra Large" variant

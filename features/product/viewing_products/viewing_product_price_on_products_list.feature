@viewing_products
Feature: Viewing a product price on products list
    In order to see the prices of listed products
    As a Visitor
    I want to be able to view a single product price on products list

    Background:
        Given the store operates on a single channel in "United States"

    @ui @api
    Scenario: Viewing a product with price on list
        Given the store has a product "T-Shirt watermelon" priced at "$19.00"
        And the store classifies its products as "T-Shirts"
        And this product belongs to "T-Shirts"
        When I browse products from taxon "T-Shirts"
        Then I should see the product "T-Shirt watermelon" with price "$19.00"

    @ui @api
    Scenario: Viewing a product with discount on list
        Given the store has a product "T-Shirt watermelon" priced at "$19.00"
        And the product "T-Shirt watermelon" has original price "$20.00"
        And the store classifies its products as "T-Shirts"
        And this product belongs to "T-Shirts"
        When I browse products from taxon "T-Shirts"
        Then I should see "T-Shirt watermelon" product discounted from "$20.00" to "$19.00"

    @ui @no-api
    Scenario: Not seeing discount on the list when the original price is lower than current price
        Given the store has a product "T-Shirt watermelon" priced at "$19.00"
        And the product "T-Shirt watermelon" has original price "$18.00"
        And the store classifies its products as "T-Shirts"
        And this product belongs to "T-Shirts"
        When I browse products from taxon "T-Shirts"
        Then I should see the product "T-Shirt watermelon" with price "$19.00"
        And I should see "T-Shirt watermelon" product not discounted on the list

    @ui @api
    Scenario: Viewing a product with a positioned default variant
        Given the store has a product "T-Shirt watermelon" priced at "$19.00"
        And the product "T-Shirt watermelon" has also an "Extra Large" variant at position 0
        And this variant is also priced at "$10.00" in "United States" channel
        And the store classifies its products as "T-Shirts"
        And this product belongs to "T-Shirts"
        When I browse products from taxon "T-Shirts"
        Then I should see the product "T-Shirt watermelon" with price "$10.00"

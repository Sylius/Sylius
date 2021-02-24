@viewing_products
Feature: Viewing a product price on products list
    In order to see the prices of listed products
    As a Visitor
    I want to be able to view a single product price on products list

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Viewing a products with price on list
        Given the store has a product "T-shirt watermelon" priced at "$19.00"
        And the store classifies its products as "T-Shirts"
        And this product belongs to "T-Shirts"
        When I browse products from taxon "T-Shirts"
        Then I should see the product "T-shirt watermelon" with price "$19.00"

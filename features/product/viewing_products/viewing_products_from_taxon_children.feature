@viewing_products
Feature: Viewing products from taxon children
    In order to browse products from general taxons
    As a Visitor
    I want to be able to view products from taxon children

    Background:
        Given the store has currency "Euro"
        And the store operates on a channel named "Poland"
        And the store classifies its products as "T-Shirts"
        And the "T-Shirts" taxon has children taxon "Men" and "Women"
        And the "Men" taxon has children taxon "XL" and "XXL"
        And the store has a product "T-Shirt Banana" available in "Poland" channel
        And this product belongs to "T-Shirts"
        And the store has a product "T-Shirt Banana For Men" available in "Poland" channel
        And this product belongs to "Men"
        And the store has a product "T-Shirt Banana For Men XXL" available in "Poland" channel
        And this product belongs to "XXL"

    @ui
    Scenario: Viewing products from taxon children
        When I browse products from taxon "T-Shirts"
        Then I should see the product "T-Shirt Banana"
        And I should see the product "T-Shirt Banana For Men"
        And I should see the product "T-Shirt Banana For Men XXL"

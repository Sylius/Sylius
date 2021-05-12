@viewing_products
Feature: Viewing products from a specific taxon and a channel
    In order to browse products that interest me most
    As a Visitor
    I want to be able to view products from a specific taxon and a channel

    Background:
        Given the store has currency "Euro"
        And the store operates on a channel named "Poland"
        And the store operates on another channel named "United States"
        And the store classifies its products as "T-Shirts" and "Funny"
        And the store has a product "T-Shirt Banana" available in "Poland" channel
        And this product belongs to "T-Shirts"
        And this product belongs to "Funny"
        And the store has a product "T-Shirt Batman" available in "United States" channel
        And this product belongs to "T-Shirts"

    @ui
    Scenario: Viewing products from a specific taxon in selected channel
        Given I am browsing channel "Poland"
        When I browse products from taxon "T-Shirts"
        Then I should see the product "T-Shirt Banana"
        And I should not see the product "T-Shirt Batman"

    @ui
    Scenario: Viewing information about empty list of products from a given taxon in selected channel
        Given I am browsing channel "United States"
        When I browse products from taxon "Funny"
        Then I should see empty list of products

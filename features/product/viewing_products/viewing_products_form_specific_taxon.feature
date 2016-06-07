@viewing_products
Feature: Viewing products from a specific taxon
    In order to see products from a specific taxon
    As a Visitor
    I want to be able to view products from a selected taxon

    Background:
        Given the store has currency "Euro"
        And the store operates on a channel named "Poland"
        And this channel has currency "Euro"
        And this channel host is "localhost"
        And the store operates on another channel named "France"
        And this channel has currency "Euro"
        And this channel host is "localhost"
        And the store classifies its products as "T-Shirts" and "Funny"
        And the store has a product "T-Shirt Banana" available in "Poland" channel
        And this product belongs to "T-Shirts"
        And this product belongs to "Funny"
        And the store has a product "Plastic Tomato" available in "Poland" channel
        And this product belongs to "Funny"
        And the store has a product "T-Shirt Batman" available in "France" channel
        And this product belongs to "T-Shirts"

    @ui
    Scenario: Viewing products from a specific taxon
        Given I want to see products in channel "Poland"
        When I check list of products for taxon "T-Shirts"
        Then I should see the product "T-Shirt Banana"
        And I should not see the product "T-Shirt Batman"
        And I should not see the product "Plastic Tomato"

    @ui
    Scenario: Viewing information about empty list of products from a given taxon
        Given I want to see products in channel "France"
        When I check list of products for taxon "Funny"
        Then I should see information about empty list of products

@viewing_products
Feature: Viewing products from a specific taxon
    In order to browse products that interest me most
    As a Visitor
    I want to be able to view products from a specific taxon

    Background:
        Given the store operates on a channel named "Poland"
        And the store classifies its products as "T-Shirts", "Funny" and "Sad"
        And the store has a product "T-Shirt Banana" available in "Poland" channel
        And this product belongs to "T-Shirts"
        And the store has a product "Plastic Tomato" available in "Poland" channel
        And this product belongs to "Funny"

    @ui @api
    Scenario: Viewing products from a specific taxon
        When I browse products from taxon "T-Shirts"
        Then I should see the product "T-Shirt Banana"
        And I should not see the product "Plastic Tomato"

    @api
    Scenario: Searching products by multiple taxons
        When I browse products from "Funny" and "T-Shirts" taxons
        Then I should see the product "T-Shirt Banana"
        And I should see the product "Plastic Tomato"

    @api
    Scenario: Searching products by multiple taxons when one of them doesn't have any products
        When I browse products from "T-Shirts" and "Sad" taxons
        Then I should see the product "T-Shirt Banana"
        And I should not see the product "Plastic Tomato"

    @ui @api
    Scenario: Viewing information about empty list of products from a given taxon
        When I browse products from taxon "Sad"
        Then I should see empty list of products

    @api
    Scenario: Searching products with non existing taxon
        When I browse products from non existing taxon
        Then I should see empty list of products

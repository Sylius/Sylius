@viewing_products
Feature: Viewing products from a specific taxon
    In order to browse products that interest me most
    As a Visitor
    I want to be able to view products from a specific taxon

    Background:
        Given the store has currency "Euro"
        And the store operates on a channel named "Poland"
        And the store classifies its products as "T-Shirts", "Funny" and "Sad"
        And the store has a product "T-Shirt Banana" available in "Poland" channel
        And this product belongs to "T-Shirts"
        And there are 10 items of product "T-shirt banana" available in the inventory
        And the store has a product "Plastic Tomato" available in "Poland" channel
        And this product belongs to "Funny"
        And there are 10 items of product "Plastic Tomato" available in the inventory

    @ui @elasticsearch @test
    Scenario: Viewing products from a specific taxon
        When I browse products from taxon "T-Shirts"
        Then I should see the product "T-Shirt Banana"
        And I should not see the product "Plastic Tomato"

    @ui @elasticsearch
    Scenario: Viewing information about empty list of products from a given taxon
        When I browse products from taxon "Sad"
        Then I should see empty list of products

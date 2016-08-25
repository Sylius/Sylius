@viewing_products
Feature: Viewing a product details
    In order to see products detailed information
    As a Visitor
    I want to be able to view a single product

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Viewing a detailed page with product's name
        Given the store has a product "T-shirt banana"
        When I check this product's details
        Then I should see the product name "T-shirt banana"

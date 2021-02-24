@viewing_products
Feature: Viewing a product price
    In order to see products price
    As a Visitor
    I want to be able to view a single product price

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Viewing a detailed page with product's price
        Given the store has a product "T-shirt banana" priced at "$39.00"
        When I check this product's details
        Then I should see the product price "$39.00"

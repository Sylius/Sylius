@viewing_products
Feature: Viewing a product discounted price
    In order to see products discounted price
    As a Visitor
    I want to be able to view a single product discounted price

    Background:
        Given the store operates on a single channel in "United States"

    @ui @api
    Scenario: Viewing a detailed page with product's original price
        Given the store has a product "T-Shirt banana" priced at "$39.00"
        And the product "T-Shirt banana" has original price "$50.00"
        When I check this product's details
        Then I should see the product price "$39.00"
        And I should see the product original price "$50.00"

    @ui @no-api
    Scenario: Viewing a detailed page without product's original price when it's higher than current price
        Given the store has a product "T-Shirt banana" priced at "$39.00"
        And the product "T-Shirt banana" has original price "$20.00"
        When I check this product's details
        Then I should see the product price "$39.00"
        And I should see this product is not discounted

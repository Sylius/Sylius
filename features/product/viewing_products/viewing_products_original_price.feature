@viewing_products
Feature: Viewing a product original price
    In order to be informed about the amount of discount
    As a Visitor
    I want to be able to view a single product original price

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Viewing a detailed page with product's original price
        Given the store has a product "T-shirt banana" priced at "$49.00"
        And this product has been originally priced at "$59.00"
        When I check this product's details
        Then I should see the product original price "$59.00"

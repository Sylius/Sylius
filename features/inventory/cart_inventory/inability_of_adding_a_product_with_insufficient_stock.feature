@cart_inventory
Feature: Inability to add a specific product to the cart when it is out of stock
    In order to buy only available products
    As a Visitor
    I want to be prevented from adding products which are not available in the inventory

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-shirt banana" priced at "$12.54"

    @ui
    Scenario: Not being able to add a product to the cart when it is out of stock
        Given the product "T-shirt banana" is out of stock
        When I check this product's details
        Then I should see that it is out of stock
        And I should be unable to add it to the cart

@cart_inventory
Feature: Prevent buying more products, than available in a stock
    In order to buy only available quantity of product's items
    As a Visitor
    I want to be prevented from adding product's items with quantity greater that amount of items available in the inventory

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-shirt Mononoke" priced at "$12.54"
        And "T-shirt Mononoke" product is tracked by the inventory
        And there are 5 items of product "T-Shirt Mononoke" available in the inventory

    @ui @javascript
    Scenario: Not being able to add some product's items to the cart if the quantity is greater than amount of items in stock
        When I add 6 products "T-shirt Mononoke" to the cart
        Then I should still be on product "T-shirt Mononoke" page
        And I should be notified that this product does not have sufficient stock

    @ui @javascript
    Scenario: Being able to add some product's items to the cart if the quantity is not greater than amount of items in stock
        When I add 4 products "T-shirt Mononoke" to the cart
        Then I should not be notified that this product does not have sufficient stock
        And I should be on my cart summary page

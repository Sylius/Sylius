@cart_inventory
Feature: Prevent buying more products than available in a stock
    In order to buy only available items
    As a Visitor
    I want to be prevented from adding items over the available amount

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt Mononoke" priced at "$12.54"
        And "T-Shirt Mononoke" product is tracked by the inventory
        And there are 5 units of product "T-Shirt Mononoke" available in the inventory

    @ui @javascript @api
    Scenario: Preventing from adding more items to the cart than it's available in stock
        When I add 6 products "T-Shirt Mononoke" to the cart
        Then I should still be on product "T-Shirt Mononoke" page
        And I should be notified that this product does not have sufficient stock

    @ui @javascript @api
    Scenario: Preventing from adding more items to the cart than it's available in stock by adding same item twice
        When I add 5 products "T-Shirt Mononoke" to the cart
        And I add again 5 products "T-Shirt Mononoke" to the cart
        Then I should still be on product "T-Shirt Mononoke" page
        And I should be notified that this product does not have sufficient stock

    @ui @javascript @api
    Scenario: Allowing to add items to the cart if they are in stock
        When I add 4 products "T-Shirt Mononoke" to the cart
        Then I should not be notified that this product does not have sufficient stock
        And I should be on my cart summary page

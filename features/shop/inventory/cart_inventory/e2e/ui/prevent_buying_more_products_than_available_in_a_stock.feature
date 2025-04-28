@cart_inventory
Feature: Preventing from buying more products than available in a stock
    In order to buy only available items
    As a Customer
    I want to be prevented from adding items over the available amount

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt Mononoke" priced at "$12.54"
        And "T-Shirt Mononoke" product is tracked by the inventory
        And there are 5 units of product "T-Shirt Mononoke" available in the inventory

    @no-api @ui @javascript
    Scenario: Allowing to add items to the cart if they are in stock
        When I add 4 products "T-Shirt Mononoke" to the cart
        Then I should not be notified that this product does not have sufficient stock
        And I should be on my cart summary page

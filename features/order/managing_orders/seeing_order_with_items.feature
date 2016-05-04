@managing_orders
Feature: Seeing an order with its items
    In order to see ordered products
    As an Administrator
    I want to be able to list items

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "Angel T-Shirt" priced at "€39.00"
        And the store has a product "Angel Mug" priced at "€19.00"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And the customer bought a single "Angel T-Shirt"
        And the customer bought a single "Angel Mug"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing order items
        When I see the "#00000666" order
        Then I should see 2 items in the list
        And I should see the product named "Angel T-Shirt" in the list
        And I should see the product named "Angel Mug" in the list
        And I should see "Items total: €58.00"
        And I should see "Total: €58.00"

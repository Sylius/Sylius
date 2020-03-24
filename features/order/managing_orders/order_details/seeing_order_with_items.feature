@managing_orders
Feature: Seeing an order with its items
    In order to see ordered products
    As an Administrator
    I want to be able to list items

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel T-Shirt" priced at "$39.00"
        And the store has a product "Angel Mug" priced at "$19.00"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought an "Angel T-Shirt" and an "Angel Mug"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Seeing order items
        When I view the summary of the order "#00000666"
        Then it should have 2 items
        And the product named "Angel T-Shirt" should be in the items list
        And the product named "Angel Mug" should be in the items list
        And the order's items total should be "$58.00"
        And the order's total should be "$58.00"

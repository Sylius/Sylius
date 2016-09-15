@checkout_inventory
Feature: Holding inventory units during checkout
    In order to be sure that products I buy were not bought by another customer
    As an Administrator
    I want to buy selected products

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Iron Maiden T-Shirt"
        And "Iron Maiden T-Shirt" product is tracked by the inventory
        And there are 5 units of product "Iron Maiden T-Shirt" available in the inventory
        And the store ships everywhere for free
        And the store allows paying offline
        And there is a customer "sylius@example.com" that placed an order "#00000022"
        And I am logged in as an administrator

    @ui
    Scenario: Holding inventory units
        Given the customer bought 3 "Iron Maiden T-Shirt" products
        And the customer chose "Free" shipping method to "United States" with "offline" payment
        When I view variants of the product "Iron Maiden T-Shirt"
        Then 3 units of this product should be on hold
        And 5 units of this product should be on hand

    @ui
    Scenario: Release hold units after order has been paid
        Given the customer bought 3 "Iron Maiden T-Shirt" products
        And the customer chose "Free" shipping method to "United States" with "offline" payment
        And this order is already paid
        When I view variants of the product "Iron Maiden T-Shirt"
        Then 2 units of this product should be on hand
        And there should be no units of this product on hold

@managing_orders
Feature: Placing an order
    In order to know about new orders
    As an Administrator
    I want to be able to manage new orders made by customers

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Iron Maiden T-Shirt"
        And the store ships everywhere for free
        And the store allows paying offline
        And there is a customer "sylius@example.com" that placed an order "#00000022"
        And I am logged in as an administrator

    @ui
    Scenario: Verifying that order has new state right after checkout
        Given the customer bought 3 "Iron Maiden T-Shirt" products
        And the customer chose "Free" shipping method to "United States" with "offline" payment
        When I browse orders
        Then I should see a single order from customer "sylius@example.com"
        And it should have a "new" state

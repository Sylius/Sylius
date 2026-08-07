@managing_orders
Feature: Browsing orders with guest or customer type indicator
    In order to distinguish guest orders from registered customer orders
    As an Administrator
    I want to see an indicator in the customer column of the orders grid

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Sylius T-Shirt"
        And the store ships everywhere for Free
        And the store allows paying with "Cash on Delivery"
        And I am logged in as an administrator

    @no-api @ui
    Scenario: Seeing a guest order indicator for an order placed by a guest
        Given the guest customer placed order with "Sylius T-Shirt" product for "guest@example.com" and "United States" based billing address with "Free" shipping method and "Cash on Delivery" payment
        When I browse orders
        Then the order placed by "guest@example.com" should be marked as a guest order

    @no-api @ui
    Scenario: Seeing a customer order indicator for an order placed by a registered customer
        Given there is a customer account "customer@example.com"
        And there is a customer "customer@example.com" that placed order with "Sylius T-Shirt" product to "United States" based billing address with "Free" shipping method and "Cash on Delivery" payment method
        When I browse orders
        Then the order placed by "customer@example.com" should be marked as a customer order

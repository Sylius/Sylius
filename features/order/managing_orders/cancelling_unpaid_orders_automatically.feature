@managing_orders
Feature: Cancelling unpaid orders automatically
    In order to get rid of completed but not paid orders
    As an Administrator
    I want to have unpaid orders automatically cancelled

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt"
        And the store ships everywhere for free
        And the store allows paying with "Paypal Express Checkout"

    @domain
    Scenario: Having order cancelled after 10 days of being unpaid
        And there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Free" shipping method to "United States" with "Paypal Express Checkout" payment
        And this order has not been paid for 10 days
        Then this order should be automatically cancelled

    @domain
    Scenario: Having unpaid order not cancelled if expiration time has not been reached
        And there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Free" shipping method to "United States" with "Paypal Express Checkout" payment
        And this order has not been paid for 2 days
        Then this order should not be cancelled

@managing_orders
Feature: Refunding order's payment
    In order to refund order's payment
    As an Administrator
    I want to be able to mark order's payment as refunded

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Green Arrow"
        And the store ships everywhere for free
        And the store allows paying offline
        And the store allows paying with "Paypal Express Checkout"
        And there is a customer "oliver@teamarrow.com" that placed an order "#00000001"
        And the customer bought a single "Green Arrow"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And this order is already paid
        And I am logged in as an administrator
        And I am viewing the summary of this order

    @ui
    Scenario: Marking order's payment as refunded
        When I mark this order's payment as refunded
        Then I should be notified that the order's payment has been successfully refunded
        And it should have payment with state refunded

    @ui
    Scenario: Marking an order as refunded after refunding all its payments
        When I mark this order's payment as refunded
        Then it should have payment with state refunded
        And it's payment state should be refunded

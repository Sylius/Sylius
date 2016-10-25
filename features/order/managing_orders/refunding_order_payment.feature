@managing_orders
Feature: Refunding order payment
    In order to refund order payment
    As an Administrator
    I want to be able to mark order payment as refunded

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Green Arrow"
        And the store ships everywhere for free
        And the store allows paying offline
        And the store allows paying with "Paypal Express Checkout"
        And there is a customer "oliver@teamarrow.com" that placed an order
        And the customer bought a single "Green Arrow"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And this order is already paid
        And I am logged in as an administrator
        And I am viewing the summary of this order


    @ui @todo
    Scenario: Marking payment as refunded
        When I mark this order's payment "Offline" as refunded
        Then I should be notified that the payment has been successfully updated
        And it should have payment "Offline" in state refunded

    @ui @todo
    Scenario: Marking order as refunded after refunding all its payments
        When I mark this order's payment "Offline" as refunded
        Then it should have payment "Offline" in state refunded
        And it should have payment state "Refunded"


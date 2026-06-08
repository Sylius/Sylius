@managing_orders
Feature: Recovering order payment state after an authorized payment is cancelled
    In order to allow the customer to retry payment after an authorization is voided by the gateway
    As an Administrator
    I want the order payment state to return to "Awaiting payment" when an authorized payment is cancelled

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for Free
        And the store has a product "PHP T-Shirt"
        And the store allows paying with "Cash on Delivery"
        And the payment method "Cash on Delivery" requires authorization before capturing
        And there is an "authorized" "#00000001" order with "PHP T-Shirt" product
        And I am logged in as an administrator

    @api @ui
    Scenario: Order payment state recovers to awaiting payment after the authorized payment is cancelled by the gateway
        When the payment of order "#00000001" is cancelled by the gateway
        Then this order should have order payment state "Awaiting payment"
        And there should be only 2 payments

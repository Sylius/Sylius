@paying_for_order
Feature: Cancelling payment request when payment method is changed
    In order to pay with the correct payment method
    As a Customer
    I want my payment requests with the previous payment method to be cancelled

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt"
        And the store ships everywhere for Free
        And the store allows paying with "Cash on Delivery"
        And this payment method is not using Payum
        And the store also allows paying with "Bank Transfer"
        And this payment method is not using Payum
        And I am a logged in customer
        And I placed an order "#00000001"
        And I bought a single "PHP T-Shirt"
        And I addressed it to "Lucifer Morningstar", "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And I chose "Free" shipping method with "Cash on Delivery" payment
        And there is a "new" "authorize" payment request for order "#00000001" using the "Cash on Delivery" payment method
        And there is also a "processing" "status" payment request for order "#00000001" using the "Cash on Delivery" payment method
        And there is also a "completed" "capture" payment request for order "#00000001" using the "Cash on Delivery" payment method

    @api @ui
    Scenario: Cancelling only not finalized payment requests when the payment method has changed
        When I want to browse order details for this order
        And I change my payment method to "Bank Transfer"
        Then a payment request with action "authorize" for payment method "Cash on Delivery" should have state "cancelled"
        And a payment request with action "status" for payment method "Cash on Delivery" should have state "cancelled"
        And a payment request with action "capture" for payment method "Cash on Delivery" should have state "completed"

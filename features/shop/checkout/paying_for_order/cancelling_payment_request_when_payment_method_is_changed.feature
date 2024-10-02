@paying_for_order
Feature: Cancelling payment request when payment method is changed
    In order to pay with the correct payment method
    As a Customer
    I want my payment request with wrong payment method to be cancelled

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt"
        And the store ships everywhere for Free
        And the store allows paying with "Cash on Delivery"
        And the store also allows paying with "Bank Transfer"
        And I am a logged in customer
        And I placed an order "#00000001"
        And I bought a single "PHP T-Shirt"
        And I addressed it to "Lucifer Morningstar", "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And I chose "Free" shipping method with "Cash on Delivery" payment
        And there is a payment request action "authorize" executed for order "#00000001" with the payment method "Cash on Delivery" and state "new"
        And there is also a payment request action "status" executed for order "#00000001" with the payment method "Cash on Delivery" and state "processing"
        And there is also a payment request action "capture" executed for order "#00000001" with the payment method "Cash on Delivery" and state "completed"

    @no-ui @api
    Scenario: Cancelling only processing or new payment requests when payment method is changed
        When I view the summary of my order "#00000001"
        And I change payment method to "Bank Transfer" after checkout
        Then my payment request with action "authorize" for payment method "Cash on Delivery" should have state "cancelled"
        And my payment request with action "status" for payment method "Cash on Delivery" should have state "cancelled"
        And my payment request with action "capture" for payment method "Cash on Delivery" should have state "completed"

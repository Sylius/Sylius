@paying_for_order
Feature: Preventing changing the payment method of a cancelled order
    In order to perform only valid order operations
    As a Customer
    I should be prevented from changing the payment method of a cancelled order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel T-Shirt"
        And the store ships everywhere for Free
        And the store allows paying with "Cash on Delivery"
        And the store also allows paying with "Bank Transfer"
        And I am a logged in customer
        And I placed an order "#00000666"
        And I bought a single "Angel T-Shirt"
        And I addressed it to "Lucifer Morningstar", "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And I chose "Free" shipping method with "Cash on Delivery" payment
        And I am changing this order's payment method
        And this order was cancelled

    @ui @api
    Scenario: Being prevented from changing the payment method of a cancelled order
        When I try to change my payment method to "Bank Transfer"
        Then I should be notified that I can no longer change payment method of this order

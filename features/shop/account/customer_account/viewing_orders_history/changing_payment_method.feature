@customer_account
Feature: Changing a payment method of a placed order
    In order to buy products
    As a Customer
    I want to be able to change a payment method of an order

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

    @ui @api
    Scenario: Changing a payment method of an order
        When I browse my orders
        And I change my payment method to "Bank Transfer"
        Then I should have "Bank Transfer" payment method on my order

    @ui @api
    Scenario: Changing a payment method of an order with a disabled payment method
        Given the payment method "Cash on Delivery" is disabled
        When I browse my orders
        And I change my payment method to "Bank Transfer"
        Then I should have "Bank Transfer" payment method on my order

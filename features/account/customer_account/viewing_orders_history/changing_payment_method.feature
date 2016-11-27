@customer_account
Feature: Changing a payment method of a placed order
    In order to buy products
    As a Customer
    I want to be able to change a payment method of an order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel T-Shirt"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And I am a logged in customer
        And I placed an order "#00000666"
        And I bought a single "Angel T-Shirt"
        And I addressed it to "Lucifer Morningstar", "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And I chose "Free" shipping method with "Cash on Delivery" payment

    @ui
    Scenario: Changing a payment method of an order
        When I browse my orders
        Then I should be able to change payment method for this order

@paying_for_order
Feature: Paying with paypal during checkout
    In order to buy products
    As a Customer
    I want to be able to pay with PayPal Express Checkout

    Background:
        Given the store operates on a single channel in "United States"
        And there is a user "john@example.com" identified by "password123"
        And the store has a payment method "PayPal" with a code "PAYPAL" and Paypal Express Checkout gateway
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And I am logged in as "john@example.com"

    @ui
    Scenario: Successful payment
        Given I added product "PHP T-Shirt" to the cart
        And I have proceeded selecting "PayPal" payment method
        When I confirm my order with paypal payment
        And I sign in to PayPal and pay successfully
        Then I should be notified that my payment has been completed
        And I should see the thank you page

    @ui
    Scenario: Cancelling the payment
        Given I added product "PHP T-Shirt" to the cart
        And I have proceeded selecting "PayPal" payment method
        When I confirm my order with paypal payment
        And I cancel my PayPal payment
        Then I should be notified that my payment has been cancelled
        And I should be able to pay again

    @ui
    Scenario: Retrying the payment with success
        Given I added product "PHP T-Shirt" to the cart
        And I have proceeded selecting "PayPal" payment method
        And I have confirmed my order with paypal payment
        But I have cancelled PayPal payment
        When I try to pay again
        And I sign in to PayPal and pay successfully
        Then I should be notified that my payment has been completed
        And I should see the thank you page

    @ui
    Scenario: Retrying the payment and failing
        Given I added product "PHP T-Shirt" to the cart
        And I have proceeded selecting "PayPal" payment method
        And I have confirmed my order with paypal payment
        But I have cancelled PayPal payment
        When I try to pay again
        And I cancel my PayPal payment
        Then I should be notified that my payment has been cancelled
        And I should be able to pay again

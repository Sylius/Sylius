@paying_for_order
Feature: Paying with paypal during checkout
    In order to buy products
    As a Customer
    I want to be able to pay with PayPal Express Checkout

    Background:
        Given the store operates on a single channel in "France"
        And there is user "john@example.com" identified by "password123"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying "PayPal Express Checkout"

    @ui
    Scenario: Successful payment
        Given I am logged in as "john@example.com"
        And I added product "PHP T-Shirt" to the cart
        And I proceed selecting "PayPal Express Checkout" payment method
        And I confirm my order with paypal payment
        When I sign in to PayPal and pay successfully
        Then I should be redirected back to the thank you page

    @ui
    Scenario: Cancelling the payment
        Given I am logged in as "john@example.com"
        And I added product "PHP T-Shirt" to the cart
        When I proceed selecting "PayPal Express Checkout" payment method
        And I confirm my order with paypal payment
        And I cancel my PayPal payment
        Then I should be redirected back to the order payment page

    @ui
    Scenario: Retrying the payment with success
        Given I am logged in as "john@example.com"
        And I added product "PHP T-Shirt" to the cart
        And I proceed selecting "PayPal Express Checkout" payment method
        And I confirm my order with paypal payment
        But I cancel my PayPal payment
        And I should be redirected back to the order payment page
        When I try to pay again
        And I sign in to PayPal and pay successfully
        Then I should be redirected back to the thank you page

    @ui
    Scenario: Retrying the payment and failing
        Given I am logged in as "john@example.com"
        And I added product "PHP T-Shirt" to the cart
        And I proceed selecting "PayPal Express Checkout" payment method
        And I confirm my order with paypal payment
        But I cancel my PayPal payment
        And I should be redirected back to the order payment page
        When I try to pay again
        And I cancel my PayPal payment
        Then I should be redirected back to the order payment page
        And I should see two cancelled payments and new one ready to be paid

@paying_for_order
Feature: Paying with credit card during checkout
    In order to buy products
    As a Customer
    I want to be able to pay with credit card

    Background:
        Given the store operates on a single channel in "United States"
        And there is a user "john@example.com" identified by "password123"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying "Stripe Checkout"
        And I am a logged in customer

    @ui
    Scenario: Successful payment
        Given I added product "PHP T-Shirt" to the cart
        And I have proceeded selecting "Stripe Checkout" payment method
        When I confirm my order
        Then I should see the thank you page

    @ui
    Scenario: Cancelling the payment
        Given I added product "PHP T-Shirt" to the cart
        And I have proceeded selecting "Stripe Checkout" payment method

    @ui
    Scenario: Retrying the payment with success
        Given I added product "PHP T-Shirt" to the cart
        And I have proceeded selecting "Stripe Checkout" payment method

    @ui
    Scenario: Retrying the payment and failing
        Given I added product "PHP T-Shirt" to the cart
        And I have proceeded selecting "Stripe Checkout" payment method

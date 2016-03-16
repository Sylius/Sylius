@checkout @ui @insulated @mink:firefox @paypal
Feature: Checkout with PayPal Express Checkout
    In order to buy products
    As a Customer
    I want to be able to pay with PayPal Express Checkout

    Background:
        Given the store operates on a single channel in "France"
        And there is user "john@example.com" identified by "password123"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying "PayPal Express Checkout"

    Scenario: Being redirected to the PayPal Express Checkout page
        Given I am logged in as "john@example.com"
        And I added product "PHP T-Shirt" to the cart
        When I proceed selecting "PayPal Express Checkout" payment method
        And I confirm my order
        Then I should be redirected to PayPal Express Checkout page

    Scenario: Successful payment
        Given I am logged in as "john@example.com"
        And I added product "PHP T-Shirt" to the cart
        And I proceed selecting "PayPal Express Checkout" payment method
        And I confirm my order
        When I sign in to PayPal and pay successfully
        Then I should be redirected back to the thank you page

    @todo
    Scenario: Cancelling the payment
        Given I added product "PHP T-Shirt" to the cart
        And I confirmed my order selecting PayPal Checkout Express payment method
        And I am on the PayPal Checkout Express page
        When I cancel my PayPal payment
        Then I should be redirected back to the order payment page
        And I should see one cancelled payment and new one ready to be paid

    @todo
    Scenario: Retrying the payment with success
        Given I added product "PHP T-Shirt" to the cart
        And I confirmed my order selecting PayPal Checkout Express payment method
        But I failed to pay
        And I am redirected back to the order payment page
        When I try to pay again
        And I sign in to PayPal and pay successfully
        Then I should be redirected back to the thank you page

    @todo
    Scenario: Retrying the payment and failing
        Given I added product "PHP T-Shirt" to the cart
        And I confirmed my order selecting PayPal Checkout Express payment method
        But I failed to pay
        And I am redirected back to the order payment page
        When I try to pay again
        And I sign in to PayPal but fail to pay
        Then I should be redirected back to the order payment page
        And I should see two failed payments and new one ready to be paid

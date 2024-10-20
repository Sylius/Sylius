@managing_payments
Feature: Browsing payment requests
    In order to have an overview of all payment requests of a specific payment
    As an Administrator
    I want to browse all payment requests of a payment

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt"
        And the store ships everywhere for Free
        And the store allows paying with "Cash on Delivery"
        And there is an "#00000001" order with "PHP T-Shirt" product
        And the payment request action "authorize" has been executed for order "#00000001" with the payment method "Cash on Delivery"
        And the payment request action "capture" has been executed for order "#00000001" with the payment method "Cash on Delivery"
        And the payment request action "sync" has been executed for order "#00000001" with the payment method "Cash on Delivery"
        And I am logged in as an administrator

    @api @ui
    Scenario: Browsing payment requests of a payment
        When I browse payments
        And I want to view the payment requests of the first payment
        Then there should be 3 payment requests on the list
        And it should be the payment request with action "authorize"
        And it should be the payment request with action "capture"
        And it should be the payment request with action "sync"

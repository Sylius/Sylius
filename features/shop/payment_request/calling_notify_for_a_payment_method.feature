@payment_request_notify
Feature: Calling notify for a payment method
    In order to process a payment request action related to a payment method
    As an external payment provider
    I want to be able to send HTTP request data to trigger a new payment request action

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "PHP T-Shirt" product
        And the store ships everywhere for Free
        And the store has a payment method "Offline" with a code "offline"
        And there is a customer "john@example.com" that placed order with "PHP T-Shirt" product to "United States" based billing address with "Free" shipping method and "Offline" payment method

    @ui
    Scenario: I want to send HTTP request to the payment method notify and succeeded
        Given this payment method is not using Payum
        When I call the payment method notify page with the code "offline"
        Then a payment request with action "notify" for payment method "Offline" should have state "completed"
        And the response status code should be 204
        And the response content should be empty

    @ui
    Scenario: I want to send HTTP request to the payment method notify and failed
        Given this payment method is not using Payum
        When I call the payment method notify page with the code "not_existing"
        Then no payment request with "notify" action should exists
        And the response status code should be 404

    @ui
    Scenario: Using Payum I want to send HTTP request to the payment method notify and succeeded
        When I call the payment method notify page with the code "offline"
        Then a payment request with action "notify" for payment method "Offline" should have state "completed"
        And the response status code should be 204
        And the response content should be empty

    @ui
    Scenario: Using Payum I want to send HTTP request to the payment method notify and failed
        When I call the payment method notify page with the code "not_existing"
        Then no payment request with "notify" action should exists
        And the response status code should be 404


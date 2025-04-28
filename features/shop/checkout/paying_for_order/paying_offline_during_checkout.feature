@paying_for_order
Feature: Paying offline during checkout
    In order to pay with cash or by external means
    As a Customer
    I want to be able to complete checkout process without paying

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And I am a logged in customer

    @api @ui
    Scenario: Successfully placing an order
        Given this payment method is not using Payum
        And I added product "PHP T-Shirt" to the cart
        And I addressed the cart
        And I chose "Free" shipping method and "Offline" payment method
        When I check the details of my cart
        And I confirm my order
        Then I should see the thank you page

    @api @ui
    Scenario: Using Payum successfully placing an order
        Given I added product "PHP T-Shirt" to the cart
        And I addressed the cart
        And I chose "Free" shipping method and "Offline" payment method
        When I check the details of my cart
        And I confirm my order
        Then I should see the thank you page

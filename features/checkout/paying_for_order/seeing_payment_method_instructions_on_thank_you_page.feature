@paying_for_order
Feature: Seeing payment method instructions on thank you page
    In order to know how to pay for my order
    As a Customer
    I want to be informed about payment instructions for chosen payment method

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store has a payment method "Offline" with a code "OFFLINE"
        And it has instructions "Account number: 0000 1111 2222 3333"

    @ui
    Scenario: Being informed about payment instructions on thank you page
        Given I am a logged in customer
        And I have product "PHP T-Shirt" in the cart
        When I proceed selecting "Offline" payment method
        And I confirm my order
        Then I should see the thank you page
        And I should be informed with "Offline" payment method instructions

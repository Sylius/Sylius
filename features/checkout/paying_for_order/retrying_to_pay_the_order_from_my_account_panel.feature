@paying_for_order
Feature: Retrying to pay the order from my account panel
    In order to complete my unpaid orders
    As a Customer
    I want to be able to access the order payment page from my account panel

    Background:
        Given the store operates on a single channel in "United States"
        And there is a user "john@example.com" identified by "password123"
        And the store allows paying "PayPal Express Checkout"
        And the store ships everywhere for free
        And I have one unpaid order #000001 with total $29.99

    @todo
    Scenario: Seeing the unpaid order in the list
        Given I am logged in as "john@example.com"
        When I view my order history
        Then I should see one order with total of $29.99 and pending payment

    @todo
    Scenario: Accessing the order payment page
        Given I am viewing my order history
        And I am logged in as "john@example.com"
        When I retry payment on my order #000001
        Then I should be redirected to PayPal Checkout Express page

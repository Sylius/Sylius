@seeing_order_summary
Feature: Seeing thank you page for already placed an order
    In order to be notice that I have placed an order
    As a Customer
    I want to be able to complete checkout process and have access for my order thank you page

    Background:
        Given the store operates on a single channel in "United States"
        And there is a user "john@example.com" identified by "password123"
        And the store has a product "PHP T-Shirt"
        And the store has a product "Pug T-Shirt"
        And the store ships everywhere for free
        And the store allows paying "Offline"

    @ui
    Scenario: Placing two orders and viewing thank you page for the first order
        Given there is a customer "sylius@example.com" that placed an order "#00000022"
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Free" shipping method to "United States" with "offline" payment
        And there is a customer "john@example.com" that placed an order "#00000023"
        And the customer bought 3 "Pug T-Shirt" products
        And the customer chose "Free" shipping method to "United States" with "offline" payment
        When I want to browse thank you page for "#00000022"
        Then I should see the thank you page

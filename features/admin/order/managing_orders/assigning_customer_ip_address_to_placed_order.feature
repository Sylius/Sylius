@managing_orders
Feature: Assigning customer's IP address to a placed order
    In order to know from which IP address a new order has been placed
    As an Administrator
    I want to have customer's IP address assigned to their orders

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And there is a customer account "customer@example.com"
        And there is a customer "customer@example.com" that placed order with "PHP T-Shirt" product to "United States" based billing address with "Free" shipping method and "Offline" payment method without completing it
        And the customer logged in as "customer@example.com"
        And the customer confirmed the order

    @api @ui
    Scenario: Verifying the customer's IP address for a newly placed order
        Given I am logged in as an administrator
        When I view the summary of the order placed by "customer@example.com"
        Then I should see this customer's IP address

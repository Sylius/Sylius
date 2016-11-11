@managing_customers
Feature: Browsing orders of a customer
    In order to see all orders of a specific customer
    As an Administrator
    I want to browse all orders of a customer

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And a customer "logan@wolverine.com" placed an order "#00000007"
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And a customer "eric@magneto.com" placed an order "#00000008"
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Browsing orders of a specific customer in the list
        When I browse orders of a customer "logan@wolverine.com"
        Then I should see a single order in the list
        And I should see the order with number "#00000007" in the list
        And I should not see the order with number "#00000008" in the list

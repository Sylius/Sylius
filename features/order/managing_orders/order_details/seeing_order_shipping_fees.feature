@managing_orders
Feature: Seeing shipping fees of an order
    In order to get to know the cost of shipping
    As an Administrator
    I want to be able to see shipping fees

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel T-Shirt" priced at "$39.00"
        And the store ships everywhere for free
        But the store has "DHL" shipping method with "$10.00" fee
        And the store allows paying offline
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought a single "Angel T-Shirt"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing free shipping of an order
        Given the customer chose "Free" shipping method to "United States" with "Offline" payment
        When I view the summary of the order "#00000666"
        Then the product named "Angel T-Shirt" should be in the items list
        And the order's items total should be "$39.00"
        And there should be a shipping charge "Free $0.00"
        And the order's shipping total should be "$0.00"
        And the order's total should be "$39.00"

    @ui
    Scenario: Seeing shipping fee of an order
        Given the customer chose "DHL" shipping method to "United States" with "Offline" payment
        When I view the summary of the order "#00000666"
        Then the product named "Angel T-Shirt" should be in the items list
        And the order's items total should be "$39.00"
        And there should be a shipping charge "DHL $10.00"
        And the order's shipping total should be "$10.00"
        And the order's total should be "$49.00"

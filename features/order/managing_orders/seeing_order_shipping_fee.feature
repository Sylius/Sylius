@managing_orders
Feature: Seeing an order's shipping fees
    In order to know cost of shipping
    As an Administrator
    I want to be able to see shipping fees

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "Angel T-Shirt" priced at "€39.00"
        And the store has "DHL" shipping method with "€10.00" fee
        And the store ships everywhere for free
        And the store allows paying offline
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought a single "Angel T-Shirt"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing order's free shipping
        Given the customer chose "Free" shipping method to "United States" with "Offline" payment
        When I see the "#00000666" order
        Then the product named "Angel T-Shirt" should be in the items list
        And the order's items total should be "€39.00"
        And the order's total should be "€39.00"
        And the order's shipping total should be "€0.00"
        And the order's shipping charges should be "Free €0.00"

    @ui
    Scenario: Seeing order's shipping fee
        Given the customer chose "DHL" shipping method to "United States" with "Offline" payment
        When I see the "#00000666" order
        Then the product named "Angel T-Shirt" should be in the items list
        And the order's items total should be "€39.00"
        And the order's total should be "€49.00"
        And the order's shipping total should be "€10.00"
        And the order's shipping charges should be "DHL €10.00"

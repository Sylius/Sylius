@managing_orders
Feature: Seeing discounts of an order
    In order to be aware of the amount of discount applied to an order
    As an Administrator
    I want to see the discount value of a specific order

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "T-Shirts"
        And the store has a product "Angel T-Shirt" priced at "$39.00"
        And it belongs to "T-Shirts"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And there is a promotion "Holiday promotion"
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing promotion discount on order while buying at least 3 items
        Given the promotion gives "$15.00" discount to every order with quantity at least 3
        And the customer bought 4 "Angel T-Shirt" products
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        When I view the summary of the order "#00000666"
        Then the order's items total should be "$141.00"
        And the order's promotion discount should be "Holiday promotion -$15.00"
        And the order's promotion total should be "-$15.00"
        And the order's total should be "$141.00"

    @ui
    Scenario: Seeing promotion discount on order's items while buying a product from a promoted taxon
        Given the promotion gives "$10.00" off on every product classified as "T-Shirts"
        And the customer bought a single "Angel T-Shirt"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        When I view the summary of the order "#00000666"
        Then the order's items total should be "$29.00"
        And the order's promotion total should be "$0.00"
        And the order's total should be "$29.00"

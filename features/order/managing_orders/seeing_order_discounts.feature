@managing_orders
Feature: Seeing an order's discounts
    In order to see discount on specific order
    As an Administrator
    I want to be able to see discount value

    Background:
        Given the store operates on a single channel in "France"
        And the store classifies its products as "T-Shirts"
        And the store has a product "Angel T-Shirt" priced at "€39.00"
        And it belongs to "T-Shirts"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And there is a promotion "Holiday promotion"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing promotion discount on order while buying at least 3 items
        Given the promotion gives "€15.00" discount to every order with quantity at least 3
        And the customer bought 4 products "Angel T-Shirt"
        When I see the "#00000666" order
        And the order's items total should be "€156.00"
        And the order's total should be "€141.00"
        And the order's promotion total should be "-€15.00"
        And the order's promotion discount should be "Holiday promotion -€15.00"

    @ui
    Scenario: Seeing promotion discount on order's items while buying a product from a promoted taxon
        Given the promotion gives "€10.00" off if order contains products classified as "T-Shirts"
        And the customer bought a single "Angel T-Shirt"
        When I see the "#00000666" order
        And the order's items total should be "€39.00"
        And the order's total should be "€29.00"
        And the order's promotion total should be "-€10.00"
        And the order's promotion discount should be "Holiday promotion -€10.00"

    @ui
    Scenario: Seeing promotion discount on order's items while buying the required number of products from a promoted taxon
        Given the promotion gives "€20.00" off if order contains 2 products classified as "T-Shirts"
        And the customer bought 3 products "Angel T-Shirt"
        When I see the "#00000666" order
        And the order's items total should be "€117.00"
        And the order's total should be "€97.00"
        And the order's promotion total should be "-€20.00"
        And the order's promotion discount should be "Holiday promotion -€20.00"

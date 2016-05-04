@managing_orders
Feature: Seeing an order's shipping fees
    In order to know cost of shipping
    As an Administrator
    I want to be able to see shipping fees

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "Angel T-Shirt" priced at "€39.00"
        And the store has "DHL" shipping method with "€10.00" fee
        And the store allows paying offline
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought a single "Angel T-Shirt"
        And the customer chose "DHL" shipping method to "United States" with "Offline" payment
        And I am logged in as an administrator

    @ui
    Scenario: Seeing order's shipping fees
        When I see the "#00000666" order
        Then I should see the product named "Angel T-Shirt" in the list
        And I should see "Items total: €39.00"
        And I should see "Total: €39.00"
        And I should see "DHL €10.00"
        And I should see "Shipping total: €10.00"

@managing_orders
Feature: Shipments are in the state "ready" after checkout
    In order to correctly process customer's shipments
    As an Administrator
    I want to have new shipments after my customer's checkout

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel T-Shirt"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought a single "Angel T-Shirt"
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And the customer chose "Free" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Checking shipment state of a placed order
        When I view the summary of the order "#00000666"
        Then it should have shipment in state "Ready"

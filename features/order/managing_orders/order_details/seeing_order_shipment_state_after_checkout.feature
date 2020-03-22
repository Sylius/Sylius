@managing_orders
Feature: Seeing shipping states of an order after checkout steps
    In order to get to know the state of shipping
    As an Administrator
    I want to be able to see shipping states

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
    Scenario: Seeing ready order shipping state
        When I browse orders
        Then order "#00000666" should have shipment state ready

    @ui
    Scenario: Seeing shipped order shipping state
        Given this order has already been shipped
        When I browse orders
        Then order "#00000666" should have shipment state shipped

    @ui
    Scenario: Seeing cancelled order shipping state
        Given the customer cancelled this order
        When I browse orders
        Then order "#00000666" should have shipment state cancelled

@managing_orders
Feature: Shipping an order
    In order to confirm shipping of an order
    As an Administrator
    I want to be able to ship a shipment

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "Angel T-Shirt"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And the customer chose "Free" shipping method with "Cash on Delivery" payment
        And the customer bought a single "Angel T-Shirt"
        And I am logged in as an administrator

    @ui
    Scenario: Finalizing order's shipment
        Given this order is ready to ship
        And I view the summary of the order "#00000666"
        When specify its tracking code as "#00044"
        And I ship this order
        Then I should be notified that the order's shipment has been successfully shipped
        And its shipment state should be "shipped"

    @ui
    Scenario: Unable to finalize shipped order's shipment
        Given this order has already been shipped
        When I view the summary of the order "#00000666"
        Then I should not be able to ship this order

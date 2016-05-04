@managing_orders
Feature: Seeing an order with basic information
    In order to see details of a specific order
    As an Administrator
    I want to be able to see order basic information

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
    Scenario: Seeing an order basics information
        When I see the "#00000666" order
        Then I should see "lucy@teamlucifer.com" customer
        And I should see "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States" shipping address
        And I should see "Mazikeen Lilim", "Pacific Coast Hwy", "90806", "Los Angeles", "United States" billing address
        And I should see "Free" shipment
        And I should see "Cash on Delivery" payment

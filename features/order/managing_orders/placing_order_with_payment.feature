@managing_orders
Feature: Payments are in the state "new" after checkout
    In order to correctly process customer's payments
    As an Administrator
    I want to have new payments after my customer's checkout

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
    Scenario: Checking payment state of a placed order
        When I view the summary of the order "#00000666"
        Then it should have payment state "New"

    @ui
    Scenario: Checking order payment state of a placed order
        When I browse orders
        Then the order "#00000666" should have order payment state "Awaiting payment"

@managing_orders
Feature: Checking payment state of a placed order
    In order to check payment state after placing an order
    As an Administrator
    I want to payment has proper state

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "Angel T-Shirt"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought a single "Angel T-Shirt"
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And the customer chose "Free" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    @todo
    Scenario: Checking payment state of a placed order
        Given I view the summary of the order "#00000666"
        Then it should have new payment state

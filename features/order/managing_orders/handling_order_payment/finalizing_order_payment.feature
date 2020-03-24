@managing_orders
Feature: Finalizing order payment
    In order to mark order's payment state as complete
    As an Administrator
    I want to be able to finalize payment

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
    Scenario: Finalizing order's payment
        Given I view the summary of the order "#00000666"
        When I mark this order as paid
        Then I should be notified that the order's payment has been successfully completed
        And it should have payment state "Completed"

    @ui
    Scenario: Unable to finalize completed order's payment
        Given this order is already paid
        When I view the summary of the order "#00000666"
        Then I should not be able to mark this order as paid again

    @ui
    Scenario: Checking the payment state of a completed order
        Given this order is already paid
        When I browse orders
        Then this order should have order payment state "Paid"

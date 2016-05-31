@managing_orders
Feature: Finalizing order payment
    In order to mark order's payment state as complete
    As an Administrator
    I want to be able to finalize payment

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
    Scenario: Finalizing order's payment
        Given I view the summary of the order "#00000666"
        When I mark this order as a paid
        Then I should be notified that the order's payment has been successfully completed
        And it should have completed payment state

    @ui
    Scenario: Unable to finalize completed order's payment
        Given this order is already paid
        When I view the summary of the order "#00000666"
        Then I should not be able to mark this order as paid again

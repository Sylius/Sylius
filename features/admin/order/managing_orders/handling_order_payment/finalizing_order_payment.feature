@managing_orders
Feature: Finalizing order's payment with untracked items
    In order to mark order's payment state as complete
    As an Administrator
    I want to be able to finalize payment

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for Free
        And the store allows paying with "Cash on Delivery"
        And the store has a product "PHP T-Shirt"
        And there is a customer "john@example.com" that placed an order "#00000001"
        And the customer bought 5 "PHP T-Shirt" products
        And the customer "John Doe" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And the customer chose "Free" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    @api @ui
    Scenario: Finalizing order's payment
        Given I am viewing the summary of the order "#00000001"
        When I mark this order as paid
        Then I should be notified that the order's payment has been successfully completed
        And it should have payment state "Completed"

    @api @ui
    Scenario: Being unable to finalize completed order's payment
        Given this order is already paid
        When I view the summary of the order "#00000001"
        And it should have payment state "Completed"
        And I should not be able to mark this order as paid again

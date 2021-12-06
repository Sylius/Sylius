@customer_account
Feature: Viewing payment status on the order show page
    In order to know whether an order has already been paid, or not
    As a Customer
    I want to see payment's status of my order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel T-Shirt" priced at "$39.00"
        And the store has a product "Angel Mug" priced at "$19.00"
        But the store has "DHL" shipping method with "$8.60" fee
        And the store allows paying with "Cash on Delivery"
        And I am a logged in customer
        And I placed an order "#00000666"
        And I bought an "Angel T-Shirt" and an "Angel Mug"
        And I addressed it to "Lucifer Morningstar", "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And I chose "DHL" shipping method with "Cash on Delivery" payment

    @ui @api
    Scenario: Seeing payment status before it's paid
        When I view the summary of my order "#00000666"
        Then I should see its payment status as "New"

    @ui @api
    Scenario: Seeing payment status after it's paid
        Given the order "#00000666" is already paid
        When I view the summary of my order "#00000666"
        And I should see its payment status as "Completed"

    @ui @api
    Scenario: Seeing order's payment status before paying all payments
        When I view the summary of my order "#00000666"
        Then I should see its order's payment status as "Awaiting payment"

    @ui @api
    Scenario: Seeing order's payment status after paying all payments
        Given the order "#00000666" is already paid
        When I view the summary of my order "#00000666"
        And I should see its order's payment status as "Paid"

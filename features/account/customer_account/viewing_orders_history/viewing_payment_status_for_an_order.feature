@customer_account
Feature: Viewing payment's status on my account panel
    In order to know what status does the order have
    As a Customer
    I want to see payment's status of my order

    Background:
        Given the store operates on a single channel in "United States"
        And there is a user "lucy@teamlucifer.com" identified by "dantesdreams"
        And the store has a product "Angel T-Shirt" priced at "$39.00"
        And the store has a product "Angel Mug" priced at "$19.00"
        But the store has "DHL" shipping method with "$8.60" fee
        And the store allows paying with "Cash on Delivery"
        And I am logged in as "lucy@teamlucifer.com"
        And I placed an order "#00000666"
        And I bought an "Angel T-Shirt" and an "Angel Mug"
        And I addressed it to "Lucifer Morningstar", "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And I chose "DHL" shipping method with "Cash on Delivery" payment

    @ui
    Scenario: Seeing payment's status before payment
        When I view the summary of the order "#00000666"
        Then I should see "New" payment status

    @ui
    Scenario: Seeing payment's status after payment
        Given the order "#00000666" is already paid
        When I view the summary of the order "#00000666"
        And I should see "Completed" payment status

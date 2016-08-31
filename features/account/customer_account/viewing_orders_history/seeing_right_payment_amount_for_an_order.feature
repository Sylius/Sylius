@customer_account
Feature: Viewing payment's amount on my account panel
    In order to know how much I have to pay for my order
    As a Customer
    I want to see the payment amount of my order with all fees

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
    Scenario: Seeing total payment
        When I view the summary of the order "#00000666"
        Then I should see "$66.60" as order's total
        And I should see that I have to pay "$66.60" for this order

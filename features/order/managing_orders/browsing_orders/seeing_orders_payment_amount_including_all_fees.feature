@managing_orders
Feature: Seeing order's payment amount including all fees
    In order to see how much a customer has to pay for his order
    As an Administrator
    I want to see the exact payment amount including all additional fees

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "VAT" tax rate of 23% for "Clothes" within the "US" zone
        And the store has a product "Angel T-Shirt" priced at "$39.00"
        And it belongs to "Clothes" tax category
        And the store has a product "Angel Mug" priced at "$19.00"
        And the store has "DHL" shipping method with "$10.00" fee
        And the store allows paying with "Cash on Delivery"
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought an "Angel T-Shirt" and an "Angel Mug"
        And the customer chose "DHL" shipping method to "United States" with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Seeing total payment
        When I view the summary of the order "#00000666"
        Then it should have 2 items
        And the product named "Angel T-Shirt" should be in the items list
        And the product named "Angel Mug" should be in the items list
        And the order's shipping total should be "$10.00"
        And the order's tax total should be "$8.97"
        And the order's items total should be "$66.97"
        And the order's total should be "$76.97"
        And the order's payment should also be "$76.97"

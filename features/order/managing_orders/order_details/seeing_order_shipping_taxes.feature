@managing_orders
Feature: Seeing taxes of an order
    In order to know shipping and product taxes
    As an Administrator
    I want to be able to see taxes in order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "VAT" tax rate of 23% for "Clothes" within the "US" zone
        And the store has "Shipping VAT" tax rate of 23% for "Shipping Services" within the "US" zone
        And the store has a product "Symfony2 T-Shirt" priced at "$140.00"
        And it belongs to "Clothes" tax category
        And the store has "DHL" shipping method with "$10.00" fee within the "US" zone
        And shipping method "DHL" belongs to "Shipping Services" tax category
        And the store allows paying offline
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing taxes of order items and shipping
        Given the customer bought a single "Symfony2 T-Shirt"
        And the customer chose "DHL" shipping method to "United States" with "Offline" payment
        When I view the summary of the order "#00000666"
        Then the order's items total should be "$172.20"
        And there should be a shipping charge "DHL $10.00"
        And the order's shipping total should be "$12.30"
        And the order's tax total should be "$34.50"
        And the order's total should be "$184.50"

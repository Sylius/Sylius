@managing_orders
Feature: Seeing an order's taxes
    In order to know shipping and product taxes
    As an Administrator
    I want to be able to see taxes in order

    Background:
        Given the store operates on a single channel
        And the store ships to "France" and "Australia"
        And there is a zone "EU" containing all members of the European Union
        And there is a zone "The Rest of the World" containing all other countries
        And default currency is "EUR"
        And default tax zone is "EU"
        And the store has "EU VAT" tax rate of 23% for "Clothes" within "EU" zone
        And the store has "Low tax" tax rate of 10% for "Clothes" for the rest of the world
        And the store has "Shipping EU VAT" tax rate of 23% for "Shipping Services" within "EU" zone
        And the store has "Shipping Low tax" tax rate of 10% for "Shipping Services" for the rest of the world
        And the store has a product "PHP T-Shirt" priced at "€80.00"
        And the store has a product "Symfony2 T-Shirt" priced at "€140.00"
        And it belongs to "Clothes" tax category
        And the store has "DHL" shipping method with "€10.00" fee within "EU" zone
        And the store has "DHL-World" shipping method with "€20.00" fee for the rest of the world
        And shipping method "DHL" belongs to "Shipping Services" tax category
        And shipping method "DHL-World" belongs to "Shipping Services" tax category
        And the store ships everything for free for the rest of the world
        And the store allows paying offline
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing order's taxes on products
        Given the customer bought a single "PHP T-Shirt"
        And the customer bought a single "Symfony2 T-Shirt"
        And the customer chose "Free" shipping method to "France" with "Offline" payment
        When I see the "#00000666" order
        Then the order's tax total should be "€32.20"
        And the order's shipping total should be "€0.00"
        And the order's total should be "€252.20"

    @ui
    Scenario: Seeing order's taxes on products and shipping
        Given the customer bought a single "Symfony2 T-Shirt"
        And the customer chose "DHL" shipping method to "France" with "Offline" payment
        When I see the "#00000666" order
        Then the order's tax total should be "€34.50"
        And the order's shipping total should be "€12.30"
        And the order's total should be "€184.50"

    @ui
    Scenario: Seeing order's taxes on products and shipping in different zone
        Given the customer bought a single "PHP T-Shirt"
        And the customer bought a single "Symfony2 T-Shirt"
        And the customer chose "DHL-World" shipping method to "Australia" with "Offline" payment
        When I see the "#00000666" order
        Then the order's tax total should be "€16.00"
        And the order's shipping total should be "€22.00"
        And the order's total should be "€256.00"

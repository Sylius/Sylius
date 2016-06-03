@managing_orders
Feature: Seeing taxes of order items
    In order to be aware of the amount of product taxes in an order
    As an Administrator
    I want to see the taxes value of a specific order

    Background:
        Given the store operates on a single channel in "France"
        And there is a zone "EU" containing all members of the European Union
        And default tax zone is "EU"
        And the store has "EU VAT" tax rate of 23% for "Clothes" within "EU" zone
        And the store has a product "PHP T-Shirt" priced at "€80.00"
        And the store has a product "Symfony2 T-Shirt" priced at "€140.00"
        And it belongs to "Clothes" tax category
        And the store ships everything for free within "EU" zone
        And the store allows paying offline
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing taxes of order items
        Given the customer bought a "PHP T-Shirt" and a "Symfony2 T-Shirt"
        And the customer chose "Free" shipping method to "France" with "Offline" payment
        When I view the summary of the order "#00000666"
        Then the order's shipping total should be "€0.00"
        And the order's tax total should be "€32.20"
        And the order's total should be "€252.20"

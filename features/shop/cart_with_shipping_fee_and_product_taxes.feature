@ui-cart
Feature: Cart with shipping taxes and product taxes
    In order to order with taxed shipping method and taxed products
    As a Customer
    I want to be aware of shipping fees and all taxes applied on my order

    Background:
        Given that store is operating on the France channel
        And default currency is "EUR"
        And there is user "john@example.com" identified by "password123"
        And store has "EU VAT" tax rate of 23% for "Clothes" in "EU" zone
        And store has "Low tax" tax rate of 10% for "Clothes" in "Rest of world" zone
        And catalog has a product "PHP T-Shirt" priced at €100.00
        And "PHP T-Shirt" tax category is "Clothes"
        And store has "DHL" shipping method with "€10.00" fee
        And "DHL" tax category is "Clothes"
        And I am logged in as "john@example.com"

    Scenario: Proper shipping fee, tax and product tax
        Given I am logged in as "john@example.com"
        And I added product "PHP T-Shirt" to the cart
        When I proceed selecting "DHL" shipping method
        Then my cart shipping fee should be "€12.30"
        And my cart taxes should be "€25.30"
        And my cart total should be "€135.30"

    Scenario: Proper shipping fee, tax and products' taxes after addressing
        Given I am logged in as "john@example.com"
        And I added 3 products "PHP T-Shirt" to the cart
        And I proceed selecting "DHL" shipping method and "Uzbekistan" as address country
        Then my cart shipping fee should be "€11.00"
        And my cart taxes should be "€31.00"
        And my cart total should be "€141.00"

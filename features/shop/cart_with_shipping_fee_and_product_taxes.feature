@ui-cart
Feature: Cart with shipping taxes and product taxes
    In order to buy goods with correct taxes and shipping fees applied
    As a Customer
    I want to have correct shipping fees and all taxes applied to my order

    Background:
        Given the store is operating on a single "France" channel
        And default currency is "EUR"
        And default tax zone is "EU"
        And there is user "john@example.com" identified by "password123"
        And store has "EU VAT" tax rate of 23% for "Clothes" within "EU" zone
        And store has "Low tax" tax rate of 10% for "Clothes" for the rest of the world
        And store has a product "PHP T-Shirt" priced at "€100.00"
        And product "PHP T-Shirt" belongs to "Clothes" tax category
        And store has "DHL" shipping method with "€10.00" fee
        And shipping method "DHL" belongs to "Clothes" tax category
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
        And I proceed selecting "DHL" shipping method and "Uzbekistan" as shipping country
        Then my cart shipping fee should be "€11.00"
        And my cart taxes should be "€31.00"
        And my cart total should be "€141.00"

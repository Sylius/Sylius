@ui-cart
Feature: Apply correct shipping fee with taxes on order
    In order to pay proper amount when buying goods and choosing taxes shipping method
    As a Customer
    I want to have correct shipping fees and taxes applied to my order

    Background:
        Given the store is operating on a single channel
        And there is "EU" zone containing all members of European Union
        And there is rest of the world zone containing all other countries
        And store ships to "France" and "Australia"
        And default currency is "EUR"
        And default tax zone is "EU"
        And there is user "john@example.com" identified by "password123"
        And store has "EU VAT" tax rate of 23% for "Clothes" within "EU" zone
        And store has "Low tax" tax rate of 10% for "Clothes" for the rest of the world
        And store has a product "PHP T-Shirt" priced at "€100.00"
        And store has "DHL" shipping method with "€10.00" fee within "EU" zone
        And store has "DHL-World" shipping method with "€20.00" fee for the rest of the world
        And shipping method "DHL" belongs to "Clothes" tax category
        And shipping method "DHL-World" belongs to "Clothes" tax category
        And store allows paying offline
        And I am logged in as "john@example.com"

    Scenario: Proper shipping fee and tax
        Given I am logged in as "john@example.com"
        And I add product "PHP T-Shirt" to the cart
        When I proceed selecting "DHL" shipping method
        Then my cart shipping fee should be "€12.30"
        And my cart taxes should be "€2.30"
        Then my cart total should be "€112.30"

    Scenario: Proper shipping fee and tax after addressing
        Given I am logged in as "john@example.com"
        And I add product "PHP T-Shirt" to the cart
        And I proceed selecting "DHL" shipping method and "Australia" as shipping country
        Then my cart shipping fee should be "€22.00"
        And my cart taxes should be "€2.00"
        Then my cart total should be "€122.00"

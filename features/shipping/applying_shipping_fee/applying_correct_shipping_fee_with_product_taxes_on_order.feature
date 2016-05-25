@applying_shipping_fee
Feature: Apply correct shipping fee with product taxes on order
    In order to pay proper amount for shipping and product taxes
    As a Customer
    I want to have correct shipping fees and all taxes applied to my order

    Background:
        Given the store operates on a single channel
        And the store ships to "Australia" and "France"
        And there is a zone "EU" containing all members of the European Union
        And there is a zone "The Rest of the World" containing all other countries
        And default currency is "EUR"
        And default tax zone is "EU"
        And the store has "EU VAT" tax rate of 23% for "Clothes" within "EU" zone
        And the store has "Low tax" tax rate of 10% for "Clothes" for the rest of the world
        And the store has "EU Shipping VAT" tax rate of 23% for "Shipping Services" within "EU" zone
        And the store has "Low Shipping VAT" tax rate of 10% for "Shipping Services" for the rest of the world
        And the store has a product "PHP T-Shirt" priced at "€100.00"
        And it belongs to "Clothes" tax category
        And the store has "DHL" shipping method with "€10.00" fee within "EU" zone
        And the store has "FedEx" shipping method with "€20.00" fee for the rest of the world
        And shipping method "DHL" belongs to "Shipping Services" tax category
        And shipping method "FedEx" belongs to "Shipping Services" tax category
        And the store allows paying offline
        And I am logged in customer

    @ui
    Scenario: Proper shipping fee, tax and product tax
        Given I have product "PHP T-Shirt" in the cart
        When I proceed selecting "DHL" shipping method
        And I choose "Offline" payment method
        Then my cart total should be "€135.30"
        And my cart taxes should be "€25.30"
        And my cart shipping total should be "€12.30"

    @ui
    Scenario: Proper shipping fee, tax and products' taxes after addressing
        Given I have 3 products "PHP T-Shirt" in the cart
        When I proceed selecting "Australia" as shipping country with "FedEx" method
        And I choose "Offline" payment method
        Then my cart total should be "€352.00"
        And my cart taxes should be "€32.00"
        And my cart shipping total should be "€22.00"

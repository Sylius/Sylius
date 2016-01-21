@ui-cart
Feature: Cart shipping with taxes
    In order to order with taxed shipping method
    As a Customer
    I want to be aware of shipping fees and taxes applied on my order

    Background:
        Given that store is operating on the France channel
        And default currency is "EUR"
        And there is user "john@example.com" identified by "password123"
        And tax rate "EU VAT" with 23% rate belongs to "Taxable Goods" category for "EU" zone
        And tax rate "Low tax" with 10% rate belongs to "Taxable Goods" category for "Rest of world" zone
        And catalog has a product "PHP T-Shirt" priced at €100.00 with no tax category
        And store has "DHL" shipping method with "€10.00" fee and "Taxable goods" tax category
        And I am logged in as "john@example.com"

    Scenario: Proper shipping fee and tax
        Given I am logged in as "john@example.com"
        And I added product "PHP T-Shirt" to the cart
        When I proceed selecting "DHL" shipping method
        Then my cart shipping fee should be "€12.30"
        And my cart taxes should be "€2.30"
        And my cart total should be "€112.30"

    Scenario: Proper shipping fee and tax after addressing
        Given I am logged in as "john@example.com"
        And I added product "PHP T-Shirt" to the cart
        And I proceed selecting "DHL" shipping method and "Uzbekistan" as address country
        Then my cart shipping fee should be "€11.00"
        And my cart taxes should be "€1.00"
        And my cart total should be "€111.00"

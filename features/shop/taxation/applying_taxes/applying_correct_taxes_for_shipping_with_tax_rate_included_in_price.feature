@applying_taxes
Feature: Applying correct taxes for shipping with tax rate included in price
    In order to pay proper amount for shipping and products
    As a Visitor
    I want to have correct taxes applied to my order

    Background:
        Given the store operates on a single channel in "United States"
        And default tax zone is "US"
        And the store has "VAT" tax rate of 5% for "Clothes" within the "US" zone
        And the store has included in price "VAT included" tax rate of 5% for "Mugs" within the "US" zone
        And the store has included in price "Shipping VAT" tax rate of 10% for "Shipping Services" within the "US" zone
        And the store has a product "PHP T-Shirt" priced at "$10.00"
        And it belongs to "Clothes" tax category
        And the store has a product "PHP Mug" priced at "$10.00"
        And it belongs to "Mugs" tax category
        And the store has "DHL" shipping method with "$10.00" fee within the "US" zone
        And shipping method "DHL" belongs to "Shipping Services" tax category

    @ui @api
    Scenario: Applying correct taxes for shipping with tax rate included in price
        When I add product "PHP T-Shirt" to the cart
        Then my cart items total should be "$10.00"
        And my cart estimated shipping cost should be "$10.00"
        And my cart taxes should be "$0.50"
        And my cart included in price taxes should be "$0.91"
        And my cart total should be "$20.50"

    @ui @api
    Scenario: Applying correct taxes for shipping with tax rate included in price
        When I add product "PHP Mug" to the cart
        Then my cart items total should be "$10.00"
        And my cart estimated shipping cost should be "$10.00"
        And my cart included in price taxes should be "$1.39"
        And my cart total should be "$20.00"

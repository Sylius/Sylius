@applying_taxes
Feature: Applying correct taxes on visitor cart for a specific date
    In order to buy goods with correct taxes applied
    As a Visitor
    I want to have up-to-date taxes applied to my order

    Background:
        Given the store operates on a single channel in "United States"
        And there is a zone "The Rest of the World" containing all other countries
        And the store ships to "Austria"
        And the store ships everywhere for Free
        And default tax zone is "US"
        And the store has "RoW VAT" tax rate of 10% for "Clothes" for the rest of the world
        And the store has "US VAT (2022)" tax rate of 23% for "Clothes" within the "US" zone ending at "31-12-2022"
        And the store has "US VAT (2023-)" tax rate of 15% for "Clothes" within the "US" zone starting at "01-01-2023"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And it belongs to "Clothes" tax category

    @ui @api
    Scenario: Applying proper taxes for product
        Given it is "01-11-2022" now
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$123.00"
        And my cart taxes should be "$23.00"

    @ui @api
    Scenario: Applying proper taxes for product
        Given it is "02-02-2023" now
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$115.00"
        And my cart taxes should be "$15.00"

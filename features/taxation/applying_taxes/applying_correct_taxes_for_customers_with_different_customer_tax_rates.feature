@applying_taxes
Feature: Applying correct taxes for customers with different customer tax rates
    In order to pay proper amount when buying goods
    As a Customer
    I want to have correct taxes applied to my order

    Background:
        Given the store operates on a single channel in "United States"
        And default tax zone is "US"
        And the store has customer tax categories "Retail" and "Wholesale"
        And the store has a customer group "General" with a "Retail" tax category
        And the store has also a customer group "Business" with a "Wholesale" tax category
        And the store has a "VAT" tax rate of 23% for "Clothes" and "Retail" customer tax category within the "US" zone
        And the store has also a "Low VAT" tax rate of 8% for "Clothes" and "Wholesale" customer tax category within the "US" zone
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And it belongs to "Clothes" tax category

    @ui @todo
    Scenario: Applying correct taxes for logged in customer
        Given there is a customer "Mike Ross" with an email "mike@ross.com" and a password "harvard"
        And this customer belongs to group "General"
        And I am logged in as "mike@ross.com"
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$123.00"
        And my cart taxes should be "$23.00"

    @ui @todo
    Scenario: Applying correct taxes for different logged in customer
        Given there is a customer "Harvey Specter" with an email "harvey@specter.com" and a password "donna"
        And this customer belongs to group "Business"
        And I am logged in as "harvey@specter.com"
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$108.00"
        And my cart taxes should be "$8.00"

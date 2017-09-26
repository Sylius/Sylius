@applying_taxes
Feature: Applying correct taxes for customers with different customer tax rates for different zones
    In order to pay proper amount when buying goods
    As a Customer
    I want to have correct taxes applied to my order

    Background:
        Given the store operates on a single channel in "United States"
        And there is a zone "The Rest of the World" containing all other countries
        And the store ships to "Germany"
        And the store ships everywhere for free
        And default tax zone is "US"
        And the store has customer tax categories "General" and "Business"
        And the store has a customer group "Retail" with a "General" tax category
        And the store has also a customer group "Wholesale" with a "Business" tax category
        And the store has a "High VAT" tax rate of 24% for "Clothes" and "General" customer tax category for the rest of the world
        And the store has also a "VAT" tax rate of 20% for "Clothes" and "Business" customer tax category for the rest of the world
        And the store has also a "Low VAT" tax rate of 10% for "Clothes" and "General" customer tax category within the "US" zone
        And the store has also a "No VAT" tax rate of 0% for "Clothes" and "Business" customer tax category within the "US" zone
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And it belongs to "Clothes" tax category
        And there is a customer "Mike Ross" with an email "mike@ross.com" and a password "harvard"
        And this customer belongs to group "Retail"
        And there is a customer "Harvey Specter" with an email "harvey@specter.com" and a password "donna"
        And this customer belongs to group "Wholesale"

    @ui
    Scenario: Applying correct taxes before specifying shipping address for logged in customer
        Given I am logged in as "mike@ross.com"
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$110.00"
        And my cart taxes should be "$10.00"

    @ui
    Scenario: Applying correct taxes before specifying shipping address for different logged in customer
        Given I am logged in as "harvey@specter.com"
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$100.00"
        And my cart taxes should be "$0.00"

    @ui
    Scenario: Applying correct taxes after specifying shipping address for logged in customer
        Given I am logged in as "mike@ross.com"
        And I have product "PHP T-Shirt" in the cart
        When I proceed selecting "Germany" as shipping country
        Then my cart total should be "$124.00"
        And my cart taxes should be "$24.00"

    @ui
    Scenario: Applying correct taxes after specifying shipping address for different logged in customer
        Given I am logged in as "harvey@specter.com"
        And I have product "PHP T-Shirt" in the cart
        When I proceed selecting "Germany" as shipping country
        Then my cart total should be "$120.00"
        And my cart taxes should be "$20.00"

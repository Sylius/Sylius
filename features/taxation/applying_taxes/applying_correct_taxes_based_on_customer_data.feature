@applying_taxes
Feature: Apply correct taxes based on customer data
    In order to pay proper amount when buying goods
    As a Customer
    I want to have correct taxes applied to my order

    Background:
        Given the store operates on a single channel in "United States"
        And there is a zone "The Rest of the World" containing all other countries
        And the store ships to "Germany"
        And the store ships everywhere for free
        And the store has "NA VAT" tax rate of 23% for "Clothes" within the "US" zone
        And the store has "VAT" tax rate of 10% for "Clothes" for the rest of the world
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And it belongs to "Clothes" tax category
        And there is user "john@example.com" with "Germany" as shipping country

    @ui
    Scenario: Proper taxes for logged in Customer
        Given I am logged in as "john@example.com"
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$110.00"
        And my cart taxes should be "$10.00"

    @ui
    Scenario: Proper taxes after specifying shipping address
        Given I am a logged in customer
        When I add product "PHP T-Shirt" to the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        Then my cart total should be "$123.00"
        And my cart taxes should be "$23.00"

    @ui
    Scenario: Proper taxes after specifying shipping address
        Given I am a logged in customer
        When I add product "PHP T-Shirt" to the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "Germany" for "Jon Snow"
        Then my cart total should be "$110.00"
        And my cart taxes should be "$10.00"

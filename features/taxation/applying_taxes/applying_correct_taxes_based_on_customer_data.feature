@applying_taxes
Feature: Apply correct taxes based on customer data
    In order to pay proper amount when buying goods
    As a Customer
    I want to have correct taxes applied to my order

    Background:
        Given the store operates on a single channel
        And the store ships to "France" and "Australia"
        And there is a zone "EU" containing all members of the European Union
        And there is a zone "The Rest of the World" containing all other countries
        And default currency is "EUR"
        And default tax zone is "EU"
        And the store has "EU VAT" tax rate of 23% for "Clothes" within "EU" zone
        And the store has "EU VAT" tax rate of 10% for "Clothes" for the rest of the world
        And the store has a product "PHP T-Shirt" priced at "€100.00"
        And it belongs to "Clothes" tax category
        And there is user "john@example.com" identified by "password123", with "Australia" as shipping country

    @ui
    Scenario: Proper taxes for taxed product
        When I add product "PHP T-Shirt" to the cart
        And I proceed logging as "john@example.com" with "password123" password
        Then my cart total should be "€110.00"
        And my cart taxes should be "€10.00"

    @ui
    Scenario: Proper taxes after specifying shipping address
        When I add product "PHP T-Shirt" to the cart
        And I log in as "john@example.com" with "password123" password
        And I proceed without selecting shipping address
        Then my cart total should be "€110.00"
        And my cart taxes should be "€10.00"

    @ui
    Scenario: Proper taxes for logged in Customer with already specified shipping address
        Given I am logged in customer
        And my default shipping address is "Australia"
        When I add product "PHP T-Shirt" to the cart
        And I proceed without selecting shipping address
        Then my cart total should be "€110.00"
        And my cart taxes should be "€10.00"

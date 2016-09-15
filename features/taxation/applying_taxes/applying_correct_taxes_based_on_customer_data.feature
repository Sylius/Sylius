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
        And there is user "john@example.com" identified by "password123", with "Germany" as shipping country
        And I am logged in as "john@example.com"

    @todo
    Scenario: Proper taxes for taxed product
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$110.00"
        And my cart taxes should be "$10.00"

    @todo
    Scenario: Proper taxes after specifying shipping address
        When I add product "PHP T-Shirt" to the cart
        And I log in as "john@example.com" with "password123" password
        And I proceed without selecting shipping address
        Then my cart total should be "$110.00"
        And my cart taxes should be "$10.00"

    @todo
    Scenario: Proper taxes for logged in Customer with already specified shipping address
        Given I am a logged in customer
        And my default shipping address is "Germany"
        When I add product "PHP T-Shirt" to the cart
        And I proceed without selecting shipping address
        Then my cart total should be "$110.00"
        And my cart taxes should be "$10.00"

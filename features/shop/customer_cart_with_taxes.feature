@ui-cart
Feature: Customer's cart with taxes
    In order to buy goods with correct taxes applied
    As a Customer
    I want to have correct taxes applied to my order

    Background:
        Given the store is operating on a single "France" channel
        And default currency is "EUR"
        And default tax zone is "EU"
        And there is user "john@example.com" identified by "password123", with "Uzbekistan" as shipping country
        And store has "EU VAT" tax rate of 23% for "Clothes" within "EU" zone
        And store has "EU VAT" tax rate of 10% for "Clothes" for the rest of the world
        And store has a product "PHP T-Shirt" priced at "€100.00"
        And product "PHP T-Shirt" belongs to "Clothes" tax category
        And store ships everything for free
        And store allows paying offline

    Scenario: Proper taxes for taxed product
        When I added product "PHP T-Shirt" to the cart
        And I proceed logging as "john@example.com"
        Then my cart taxes should be "€10.00"
        And my cart total should be "€110.00"

    Scenario: Proper taxes after specifying shipping address
        When I added product "PHP T-Shirt" to the cart
        And I log in as "john@example.com"
        Then my cart taxes should be "€10.00"
        And my cart total should be "€110.00"

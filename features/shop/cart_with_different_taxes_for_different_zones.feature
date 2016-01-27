@ui-cart
Feature: Apply taxes based on zone and tax rate
    In order to buy goods with correct taxes applied
    As a Customer
    I want to have correct taxes applied to my order

    Background:
        Given the store is operating on a single "France" channel
        And default currency is "EUR"
        And default tax zone is "EU"
        And store has "EU VAT" tax rate of 23% for "Clothes" within "EU" zone
        And store has "No tax" tax rate of 0% for "Clothes" for the rest of the world
        And store has "Low VAT" tax rate of 5% for "Mugs" for the rest of the world
        And store has a product "PHP T-Shirt" priced at "€100.00"
        And product "PHP T-Shirt" belongs to "Clothes" tax category
        And store has a product "Symfony Mug" priced at "€50.00"
        And product "Symfony Mug" belongs to "Mugs" tax category
        And store ships everything for free
        And store allows paying offline
        And I am logged in customer

    Scenario: Displaying correct tax before specifying shipping address
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "€123.00"
        And my cart taxes should be "€23.00"

    Scenario: Displaying correct tax after specifying shipping address
        When I add product "PHP T-Shirt" to the cart
        And I proceed selecting "Offline" payment method and "Uzbekistan" as shipping country
        Then my cart total should be "€100.00"
        And my cart taxes should be "€0.00"

    Scenario: Displaying correct taxes for multiple products after specifying shipping address
        When I add 3 products "PHP T-Shirt" to the cart
        And I proceed selecting "Offline" payment method and "Uzbekistan" as shipping country
        Then my cart total should be "€300.00"
        And my cart taxes should be "€0.00"

    Scenario: Displaying correct taxes for multiple products from different zones before specifying shipping address
        When I add product "PHP T-Shirt" to the cart
        And I add 2 products "Symfony Mug" to the cart
        Then my cart total should be "€228.00"
        And my cart taxes should be "€28.00"

    Scenario: Displaying correct taxes for multiple products from different zones after specifying shipping address
        When I add product "PHP T-Shirt" to the cart
        And I add 2 products "Symfony Mug" to the cart
        And I proceed selecting "Offline" payment method and "Uzbekistan" as shipping country
        Then my cart total should be "€223.00"
        And my cart taxes should be "€23.00"

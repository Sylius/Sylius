@ui-cart
Feature: Apply correct taxes for products with different tax rates for different zones
    In order to pay proper amount when buying goods having different tax rates for different zones
    As a Customer
    I want to have correct taxes applied to my order

    Background:
        Given the store is operating on a single channel
        And store ships to "Australia" and "France"
        And there is "EU" zone containing all members of European Union
        And there is rest of the world zone containing all other countries
        And default currency is "EUR"
        And default tax zone is "EU"
        And store has "EU VAT" tax rate of 23% for "Clothes" within "EU" zone
        And store has "No tax" tax rate of 0% for "Clothes" for the rest of the world
        And store has "Low VAT" tax rate of 5% for "Mugs" for the rest of the world
        And store has a product "PHP T-Shirt" priced at "€100.00"
        And product "PHP T-Shirt" belongs to "Clothes" tax category
        And store has a product "Symfony Mug" priced at "€50.00"
        And product "Symfony Mug" belongs to "Mugs" tax category
        And store ships everything for free within "EU" zone
        And store ships everything for free for the rest of the world
        And store allows paying offline
        And there is user "john@example.com" identified by "password123"
        And I am logged in as "john@example.com"

    Scenario: Displaying correct tax before specifying shipping address
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "€123.00"
        And my cart taxes should be "€23.00"

    Scenario: Displaying correct tax after specifying shipping address
        When I add product "PHP T-Shirt" to the cart
        And I proceed selecting "Australia" as shipping country with "Offline" payment method
        Then my cart total should be "€100.00"
        And my cart taxes should be "€0.00"

    Scenario: Displaying correct taxes for multiple products after specifying shipping address
        When I add 3 products "PHP T-Shirt" to the cart
        And I proceed selecting "Australia" as shipping country with "Offline" payment method
        Then my cart total should be "€300.00"
        And my cart taxes should be "€0.00"

    Scenario: Displaying correct taxes for multiple products from different zones before specifying shipping address
        When I add product "PHP T-Shirt" to the cart
        And I add 2 products "Symfony Mug" to the cart
        Then my cart total should be "€223.00"
        And my cart taxes should be "€23.00"

    Scenario: Displaying correct taxes for multiple products from different zones after specifying shipping address
        When I add product "PHP T-Shirt" to the cart
        And I add 2 products "Symfony Mug" to the cart
        And I proceed selecting "Australia" as shipping country with "Offline" payment method
        Then my cart total should be "€205.00"
        And my cart taxes should be "€5.00"

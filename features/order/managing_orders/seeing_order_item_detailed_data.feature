@managing_orders
Feature: Seeing order item detailed data
    In order to be aware of the every important information about each order item
    As an Administrator
    I want to see detailed data about items in order

    Background:
        Given the store operates on a single channel in "France"
        And there is a zone "EU" containing all members of the European Union
        And default tax zone is "EU"
        And the store has "EU VAT" tax rate of 10% for "T-Shirts" within "EU" zone
        And the store classifies its products as "T-Shirts"
        And the store has a product "Iron Man T-Shirt" priced at "€39.00"
        And it belongs to "T-Shirts" tax category
        And the store ships everything for free within "EU" zone
        And the store allows paying with "Cash on Delivery"
        And there is a promotion "#teamIronMan promotion"
        And it gives "€12.00" discount to every order with quantity at least 3
        And there is a promotion "T-Shirts promotion"
        And it gives "€2.00" off on every product with minimum price at "€20.00"
        And there is a customer "tony@stark.com" that placed an order "#00000666"
        And the customer bought 4 "Iron Man T-Shirt" products
        And the customer chose "Free" shipping method to "France" with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Seeing details of item in one row
        Given I view the summary of the order "#00000666"
        When I check "Iron Man T-Shirt" data
        Then its unit price should be €39.00
        And its discounted unit price should be €37.00
        And its quantity should be 4
        And its subtotal should be €148.00
        And its discount should be -€12.00
        And its tax should be €13.60
        And its total should be €149.60

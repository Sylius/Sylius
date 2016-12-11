@managing_orders
Feature: Seeing order item detailed data
    In order to be aware of the every important information about each order item
    As an Administrator
    I want to see detailed data about items in order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "VAT" tax rate of 10% for "T-Shirts" within the "US" zone
        And the store classifies its products as "T-Shirts"
        And the store has a product "Marvel T-Shirt"
        And this product has "Iron Man T-Shirt" variant priced at "$49.00"
        And this product has "Thor T-Shirt" variant priced at "$39.00"
        And "Iron Man T-Shirt" variant of product "Marvel T-Shirt" belongs to "T-Shirts" tax category
        And the store ships everything for free within the "US" zone
        And the store allows paying with "Cash on Delivery"
        And there is a promotion "#teamIronMan promotion"
        And it gives "$12.00" discount to every order with quantity at least 3
        And there is a promotion "T-Shirts promotion"
        And it gives "$2.00" off on every product with minimum price at "$20.00"
        And there is a customer "tony@stark.com" that placed an order "#00000666"
        And the customer bought 4 units of "Iron Man T-Shirt" variant of product "Marvel T-Shirt"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Seeing details of item in one row
        Given I view the summary of the order "#00000666"
        When I check "Iron Man T-Shirt" data
        Then its code should be "IRON_MAN_T_SHIRT"
        And its unit price should be $49.00
        And its discounted unit price should be $47.00
        And its quantity should be 4
        And its subtotal should be $188.00
        And its discount should be -$12.00
        And its tax should be $17.60
        And its total should be $193.60

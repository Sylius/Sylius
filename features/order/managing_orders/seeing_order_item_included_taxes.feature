@managing_orders
Feature: Seeing included in price taxes of order items
    In order to be aware of the amount of product taxes in an order
    As an Administrator
    I want to see included in price taxes value of a specific order

    Background:
        Given the store operates on a single channel in "France"
        And there is a zone "EU" containing all members of the European Union
        And default tax zone is "EU"
        And the store has included in price "Guns tax" tax rate of 10% for "Guns" within "EU" zone
        And the store has a product "Winchester M1866" priced at "€220.00"
        And it belongs to "Guns" tax category
        And the store ships everything for free within "EU" zone
        And the store allows paying offline
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing included in price taxes of order items are not counted in taxes total
        Given the customer bought 2 "Winchester M1866" products
        And the customer chose "Free" shipping method to "France" with "Offline" payment
        When I view the summary of the order "#00000666"
        And I check "Winchester M1866" data
        Then its tax should be €40.00
        And the order's shipping total should be "€0.00"
        And the order's tax total should be "€40.00"
        And the order's total should be "€440.00"

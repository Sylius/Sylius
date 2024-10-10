@managing_orders
Feature: Seeing order with different promotions
    In order to be aware of the amount of product promotions in an order
    As an Administrator
    I want to see an item price with all promotion discounts

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And the store has a product "PHP T-Shirt" priced at "$60.00"
        And the store has a product "Symfony Mug" priced at "$40.00"
        And there is a promotion "T-Shirts promotion"
        And it gives "$20.00" off on a "PHP T-Shirt" product
        And there is a promotion "Holiday promotion"
        And it gives "$10.00" discount to every order
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And I am logged in as an administrator

    @api @ui
    Scenario: Seeing prices and discount prices of order item
        Given the customer bought 2 "PHP T-Shirt" products
        And the customer bought a single "Symfony Mug"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        When I view the summary of the order "#00000666"
        Then the "PHP T-Shirt" product's unit price should be "$60.00"
        And the "PHP T-Shirt" product's discounted unit price should be "$36.66"
        And the "PHP T-Shirt" product's quantity should be 2
        And the "PHP T-Shirt" product's item discount should be "-$20.00"
        And the "PHP T-Shirt" product's order discount should be "~ -$3.34"
        And the "PHP T-Shirt" product's subtotal should be "$73.33"
        And the "Symfony Mug" product's unit price should be "$40.00"
        And the "Symfony Mug" product's discounted unit price should be "$36.67"
        And the "Symfony Mug" product's quantity should be 1
        And the "Symfony Mug" product's item discount should be "$0.00"
        And the "Symfony Mug" product's order discount should be "~ -$3.33"
        And the "Symfony Mug" product's subtotal should be "$36.67"

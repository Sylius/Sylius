@managing_orders
Feature: Seeing order with different promotions
    In order to be aware of the amount of product promotions in a order
    As an Administrator
    I want to see an item price with all promotion discount

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for free
        And the store allows paying offline
        And the store has a product "PHP T-Shirt" priced at "$60.00"
        And the store has a product "PHP Mug" priced at "$40.00"
        And there is a promotion "T-Shirts promotion"
        And it gives "$20.00" off on a "PHP T-Shirt" product
        And there is a promotion "Holiday promotion"
        And it gives "$10.00" discount to every order
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing prices and discount prices of order item
        Given the customer bought 2 "PHP T-Shirt" products
        And the customer bought a single "PHP Mug"
        And the customer chose "Free" shipping method to "United States" with "offline" payment
        When I view the summary of the order "#00000666"

        Then the product's "PHP T-Shirt" unit price should be "$60.00"
        And the product's "PHP T-Shirt" item discount should be "-$20.00"
        And the product's "PHP T-Shirt" order discount should be "-$3.00"
        And the product's "PHP T-Shirt" discounted unit price should be "$37.00"
        And the product's "PHP T-Shirt" quantity should be 2
        And the product's "PHP T-Shirt" subtotal should be "$74.00"

#        And the product's "PHP Mug" unit price should be "$40.00"
#        And the product's "PHP Mug" item discount should be "-$0.00"
#        And the product's "PHP Mug" order discount should be "-$4.00"
#        And the product's "PHP Mug" discounted unit price should be "$36.00"
#        And the product's "PHP Mug" quantity should be 1
#        And the product's "PHP Mug" subtotal should be "$36.00"

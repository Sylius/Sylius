@applying_shipping_method_rules
Feature: Seeing estimated shipping costs based on total weight
    In order to be aware of estimated shipping costs
    As a Customer
    I want to see estimated shipping costs that match the shipping method rule

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Jacket for the Lochness Monster" priced at "$1,337.00"
        And this product's weight is 200
        And the store has a product "T-Shirt for Tinkerbell" priced at "$1.00"
        And this product's weight is 0.1
        And the store has "Heavy Duty Courier" shipping method with "$200.00" fee
        And this shipping method is only available for orders with a total weight greater or equal to 100.0
        And the store has "Fairytale Delivery Service" shipping method with "$2.00" fee
        And this shipping method is only available for orders with a total weight less or equal to 1.0
        And I am a logged in customer

    @ui @api
    Scenario: Seeing valid estimated shipping cost for the cart with a total weight over minimum total weight configured on the shipping method
        When I add product "Jacket for the Lochness Monster" to the cart
        Then my cart estimated shipping cost should be "$200.00"

    @ui @api
    Scenario: Seeing valid estimated shipping cost for the cart with a total weight under maximum total weight configured on the shipping method
        When I add product "T-Shirt for Tinkerbell" to the cart
        Then my cart estimated shipping cost should be "$2.00"

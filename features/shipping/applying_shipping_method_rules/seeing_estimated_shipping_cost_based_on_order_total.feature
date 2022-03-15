@applying_shipping_method_rules
Feature: Seeing estimated shipping costs based on order total
    In order to be aware of estimated shipping costs
    As a Customer
    I want to see estimated shipping costs that match the shipping method rule

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Cheap Jacket" priced at "$20.00"
        And the store has a product "Expensive Jacket" priced at "$200.00"
        And the store has "Ship with us, ship with pride" shipping method with "$200" fee
        And this shipping method is only available for orders over or equal to "$30"
        And the store has "We delivery cheap goodz" shipping method with "$2" fee
        And this shipping method is only available for orders under or equal to "$29.99"
        And I am a logged in customer

    @ui @api
    Scenario: Seeing estimated shipping cost that handle expensive goods
        When I add product "Expensive Jacket" to the cart
        Then my cart estimated shipping cost should be "$200.00"

    @ui @api
    Scenario: Seeing estimated shipping cost that handle cheap goods
        Given I add product "Cheap Jacket" to the cart
        Then my cart estimated shipping cost should be "$2.00"

@applying_shipping_method_rules
Feature: Seeing estimated shipping costs based on items total
    In order to be aware of estimated shipping costs
    As a Customer
    I want to see estimated shipping costs that match the shipping method rule

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Cheap Jacket" priced at "$20.00"
        And the store has a product "Expensive Jacket" priced at "$30.00"
        And the store has "Above $30" shipping method with "$1.00" fee
        And this shipping method is only available for orders over or equal to "$30.00"
        And the store has "Below $29.99" shipping method with "$10.00" fee
        And this shipping method is only available for orders under or equal to "$29.99"
        And I am a logged in customer

    @ui @api
    Scenario: Seeing valid estimated shipping cost for the cart with a value over minimum price configured on the shipping method
        When I add product "Expensive Jacket" to the cart
        Then my cart estimated shipping cost should be "$1.00"

    @ui @api
    Scenario: Seeing valid estimated shipping cost for the cart with a value under maximum price configured on the shipping method
        When I add product "Cheap Jacket" to the cart
        Then my cart estimated shipping cost should be "$10.00"

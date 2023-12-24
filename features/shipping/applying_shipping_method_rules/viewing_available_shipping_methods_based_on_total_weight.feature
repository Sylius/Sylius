@applying_shipping_method_rules
Feature: Viewing available shipping methods based on total weight
    In order to only see applicable shipping methods
    As an Customer
    I want to see the shipping methods that are available to my order based on the total weight of my order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Jacket for the Lochness Monster" priced at "$1,337.00"
        And this product's weight is 200
        And the store has a product "T-Shirt for Tinkerbell" priced at "$1.00"
        And this product's weight is 0.1
        And the store has "DHL" shipping method with "$20.00" fee
        And the store has "Heavy Duty Courier" shipping method with "$150.00" fee
        And this shipping method is only available for orders with a total weight greater or equal to 100.0
        And the store has "Fairytale Delivery Service" shipping method with "$2.00" fee
        And this shipping method is only available for orders with a total weight less or equal to 1.0
        And I am a logged in customer

    @ui @api
    Scenario: Seeing shipping methods that handle heavy goods
        Given I have product "Jacket for the Lochness Monster" in the cart
        When I specified the billing address
        Then I should be on the checkout shipping step
        And I should see "DHL" shipping method
        And I should see "Heavy Duty Courier" shipping method
        And I should not see "Fairytale Delivery Service" shipping method

    @ui @api
    Scenario: Seeing shipping methods that handle light goods
        Given I have product "T-Shirt for Tinkerbell" in the cart
        When I specified the billing address
        Then I should be on the checkout shipping step
        And I should see "DHL" shipping method
        And I should see "Fairytale Delivery Service" shipping method
        And I should not see "Heavy Duty Courier" shipping method

    @ui @api
    Scenario: Seeing shipping methods that handle all goods
        Given I have product "T-Shirt for Tinkerbell" in the cart
        And I add 11 of them to my cart
        When I specified the billing address
        Then I should be on the checkout shipping step
        And I should see "DHL" shipping method
        And I should not see "Fairytale Delivery Service" shipping method
        And I should not see "Heavy Duty Courier" shipping method

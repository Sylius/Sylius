@applying_promotion_rules
Feature: Reapplying promotion on cart change
    In order to receive proper discount for my order
    As a Customer
    I want to have proper discount applied after every operation on my cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And there is a promotion "Holiday promotion"
        And I am a logged in customer

    @api @ui
    Scenario: Not receiving discount on shipping after removing last item from cart
        Given the store has "DHL" shipping method with "$10.00" fee
        And the promotion gives "100%" discount on shipping to every order
        And I added product "PHP T-Shirt" to the cart
        And I addressed the cart
        And I chose "DHL" shipping method
        And I removed product "PHP T-Shirt" from the cart
        When I check the details of my cart
        Then my cart should be empty
        And there should be no shipping fee
        And there should be no discount applied

    @api @ui
    Scenario: Receiving discount on shipping after shipping method change
        Given the store has "DHL" shipping method with "$10.00" fee
        And the store has "FedEx" shipping method with "$30.00" fee
        And the promotion gives "100%" discount on shipping to every order
        And I added product "PHP T-Shirt" to the cart
        And I addressed the cart
        And I chose "DHL" shipping method
        When I decide to change shipping method
        And I change shipping method to "FedEx"
        And I complete the shipping step
        And I check the details of my cart
        Then my cart total should be "$100.00"
        And my cart shipping should be for free

    @api @ui
    Scenario: Receiving discount after removing an item from the cart and then adding another one
        Given the store has a product "Symfony T-Shirt" priced at "$150.00"
        And the promotion gives "$10.00" discount to every order
        And I added product "PHP T-Shirt" to the cart
        And I removed product "PHP T-Shirt" from the cart
        And I added product "Symfony T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "$140.00"
        And my discount should be "-$10.00"

    @api @ui
    Scenario: Not receiving discount when cart does not meet the required total value after removing an item
        Given the promotion gives "$10.00" discount to every order with items total at least "$120.00"
        And I added 2 products "PHP T-Shirt" to the cart
        And I changed product "PHP T-Shirt" quantity to 1 in my cart
        When I check the details of my cart
        Then my cart total should be "$100.00"
        And there should be no discount applied

    @api @ui
    Scenario: Not receiving discount when cart does not meet the required quantity after removing an item
        Given the promotion gives "$10.00" discount to every order with quantity at least 3
        And I added 3 products "PHP T-Shirt" to the cart
        And I changed product "PHP T-Shirt" quantity to 1 in my cart
        When I check the details of my cart
        Then my cart total should be "$100.00"
        And there should be no discount applied

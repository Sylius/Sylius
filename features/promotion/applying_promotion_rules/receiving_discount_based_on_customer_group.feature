@applying_promotion_rules
Feature: Receiving discount based on customer group
    In order to pay less while I belong to specific group
    As a Customer
    I want to receive discount for my purchase when I belong to customer group with dedicated promotion

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And the store has a customer group "Platinum"
        And the store has a customer group "Retail"
        And there is a promotion "Platinum customers promotion"
        And the promotion gives "$20.00" off if a customer belongs to group "Platinum"
        And there is a customer "John Doe" identified by an email "john.doe@xample.com" and a password "1234_O"
        And I am logged in as "john.doe@xample.com"

    @todo
    Scenario: Receiving discount on order while my group is correct
        Given I was assigned to group "Platinum"
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$80.00"
        And my discount should be "-$20.00"

    @todo
    Scenario: Receiving no discount on order while my group is incorrect
        Given I was assigned to group "Retail"
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$100.00"
        And there should be no discount

    @todo
    Scenario: Receiving no discount on order while I am not assigned to any group
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$100.00"
        And there should be no discount

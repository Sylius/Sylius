@applying_promotion_rules
Feature: Reverting previously applied discount on cart
    In order to get discount only on specific case
    As a Customer
    I want to have previously applied promotion reverted

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "PHP T-Shirt" priced at "€100.00"
        And the store has a product "PHP Mug" priced at "€20.00"
        And there is a promotion "Christmas promotion"
        And the promotion gives "€10.00" discount to every order with quantity at least 2

    @ui
    Scenario: Reverting discount applied from quantity based promotion
        Given I have product "PHP Mug" in the cart
        And I have product "PHP T-Shirt" in the cart
        When I remove product "PHP T-Shirt" from the cart
        Then my cart total should be "€20.00"
        And there should be no discount

@applying_promotion_rules
Feature: Reverting previously applied discount on cart
    In order to get discount only on specific case
    As a Customer
    I want to have previously applied promotion reverted

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And the store has a product "PHP Mug" priced at "$20.00"
        And there is a promotion "Christmas promotion"

    @ui @javascript
    Scenario: Reverting discount applied from total item quantity based promotion
        Given this promotion gives "$10.00" discount to every order with quantity at least 2
        And I have product "PHP Mug" in the cart
        And I have product "PHP T-Shirt" in the cart
        When I remove product "PHP T-Shirt" from the cart
        Then my cart total should be "$20.00"
        And there should be no discount

    @ui
    Scenario: Reverting discount applied from total item cost based promotion
        Given this promotion gives "10%" off on every product when the item total is at least "$100.00"
        And I have 8 products "PHP Mug" in the cart
        When I change "PHP Mug" quantity to 4
        Then product "PHP Mug" price should not be decreased
        And my cart total should be "$80.00"

    @ui
    Scenario: Reverting discount applied from total item cost based promotion
        Given this promotion gives "10%" off on every product when the item total is at least "$100.00"
        And I have 8 products "PHP Mug" in the cart
        When I change "PHP Mug" quantity to 4
        Then product "PHP Mug" price should not be decreased
        And my cart total should be "$80.00"

@receiving_discount
Feature: Reverting previously applied discount on cart
    In order to get discount only on specific case
    As a Customer
    I want to have previously applied promotion reverted

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "PHP T-Shirt" priced at "€100.00"
        And the store has a product "PHP Mug" priced at "€20.00"
        And there is a promotion "Christmas promotion"
        And the promotion gives "€10.00" off on every product priced between "€15.00" and "€50.00"

    @ui
    Scenario: Receiving fixed discount on a single item fulfilling minimum price criteria
        Given I have product "PHP Mug" in the cart
        When I add product "PHP T-Shirt" to the cart
        Then its price should not be decreased
        And product "PHP Mug" price should not be decreased
        And my cart total should be "€120.00"

@applying_promotion_coupon
Feature: Applying promotion coupon with rules
    In order to pay proper amount after using the promotion coupon
    As a Visitor
    I want to have promotion coupon's discounts applied to my cart only if I meet the condition of a promotion coupon

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Fashion" taxonomy
        And the store has a product "PHP T-Shirt" in the "Fashion" taxon at 1st position priced at "$100.00"

    @api @ui
    Scenario: Receiving discount from promotion with total price of items from taxon rule
        Given there is a promotion "Christmas sale" with "Total price of items from taxon" rule configured with "Fashion" taxon and "$90.00" amount for "United States" channel of coupon code "SANTA2024"
        And this promotion gives "75%" discount to every order
        When I add product "PHP T-Shirt" to the cart
        And I use coupon with code "SANTA2024"
        Then I should be notified that the cart has been updated
        And my cart total should be "$25.00"
        And my discount should be "-$75.00"

    @api @ui
    Scenario: Receiving discount from promotion with total price of items from taxon rule applied twice
        Given there is a promotion "Christmas sale" with "Total price of items from taxon" rule configured with "Fashion" taxon and "$90.00" amount for "United States" channel of coupon code "SANTA2024"
        And this promotion gives "75%" discount to every order
        When I add product "PHP T-Shirt" to the cart
        And I use coupon with code "SANTA2024"
        And I use coupon with code "SANTA2024"
        Then I should be notified that the cart has been updated
        And my cart total should be "$25.00"
        And my discount should be "-$75.00"

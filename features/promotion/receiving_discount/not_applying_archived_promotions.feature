@receiving_discount
Feature: Not applying archived promotions to the cart
    In order to not apply promotions that are archived
    As a Visitor
    I want to not receive discounts from archived promotions

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And there is a promotion "Christmas sale"
        And this promotion gives "$10.00" discount to every order

    @api @ui
    Scenario: Archived promotion is not applied to the cart
        Given the promotion "Christmas sale" is archived
        When I add product "PHP T-Shirt" to the cart
        Then I should be on my cart summary page
        And I should be notified that the product has been successfully added
        And my cart total should be "$100.00"

@checkout
Feature: Not seeing payment method instructions on thank you page if order total is zero
    In order to not being wrongly informed about my order's payments
    As a Customer
    I want to not see payment instructions if order total is zero

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$10.00"
        And the store ships everywhere for free
        And there is a promotion "Holiday promotion"
        And the promotion gives "$10.00" discount to every order with quantity at least 1
        And I am a logged in customer

    @ui
    Scenario: Not being informed about payment instructions on thank you page
        Given I have product "PHP T-Shirt" in the cart
        When I proceed selecting "Free" shipping method
        And I confirm my order
        Then I should see the thank you page
        And I should not see any instructions about payment method
        And I should not be able to change payment method

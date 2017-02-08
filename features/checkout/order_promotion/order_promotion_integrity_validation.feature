@checkout
Feature: Order promotions integrity
    In order to have valid promotions applied on my order
    As a Customer
    I want to have information about promotion changes on my order

    Background:
        Given the store operates on a single channel in "United States"
        And the store allows paying offline
        And the store ships everywhere for free
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And there is a promotion "Christmas sale"
        And this promotion gives "$10.00" discount to every order
        And this promotion expires tomorrow
        And I am a logged in customer

    @ui
    Scenario: Preventing customer from completing checkout with already expired promotion
        Given I added product "PHP T-Shirt" to the cart
        And I have proceeded selecting "Offline" payment method
        And this promotion has already expired
        When I confirm my order
        Then I should be informed that this promotion is no longer applied
        And I should not see the thank you page

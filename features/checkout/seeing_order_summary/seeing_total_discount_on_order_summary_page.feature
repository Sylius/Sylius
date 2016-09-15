@checkout
Feature: Seeing order promotion total on order summary page
    In order to see what kind of discounts I received
    As a Customer
    I want to be able to see promotion total on the order summary page

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "The Sorting Hat" priced at "$20.00"
        And the store ships everywhere for free
        And the store allows paying offline
        And there is a promotion "Holiday promotion"
        And it gives "20%" discount to every order
        And there is a promotion "All year promotion"
        And it gives "$5.00" discount to every order
        And I am a logged in customer

    @ui
    Scenario: Seeing the total discount on order summary page
        Given I have product "The Sorting Hat" in the cart
        When I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "Free" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And my order promotion total should be "-$9.00"
        And "Holiday promotion" should be applied to my order
        And "All year promotion" should be applied to my order

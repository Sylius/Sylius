@checkout
Feature: Sorting payment method selection
    In order to see the most suitable payment methods first
    As a Customer
    I want to have them already sorted

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Targaryen T-Shirt" priced at "$19.99"
        And the store allows shipping with "Aardvark Stagecoach"
        And the store allows paying with "Paypal Express Checkout" at position 0
        And the store allows paying with "Cash on Delivery" at position 2
        And the store allows paying with "Offline" at position 1
        And I am a logged in customer

    @ui
    Scenario: Seeing payment methods sorted
        Given I have product "Targaryen T-Shirt" in the cart
        When I am at the checkout addressing step
        And I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I select "Aardvark Stagecoach" shipping method
        And I complete the shipping step
        Then I should have "Paypal Express Checkout" payment method available as the first choice
        And I should have "Cash on Delivery" payment method available as the last choice

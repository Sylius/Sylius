@checkout
Feature: Seeing an order shipping method and payment method details on summary page
    In order to be certain about a shipping method and payment method
    As a Customer
    I want to be able to see all details of chosen shipping method and payment method

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Lannister Coat" priced at "$19.99"
        And the store allows shipping with "Cash on delivery"
        And the store allows paying "offline"
        And I am a logged in customer

    @ui
    Scenario: Seeing shipping method and payment method
        Given I have product "Lannister Coat" in the cart
        When I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "Cash on delivery" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And my order's shipping method should be "Cash on delivery"
        And my order's payment method should be "Offline"

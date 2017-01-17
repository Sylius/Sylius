@checkout
Feature: Emptying the cart after checkout
    In order to start a new order after purchase
    As a Customer
    I want my cart to be cleared after I place an order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Sig Sauer P226" priced at "$499.99"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And I am a logged in customer

    @ui
    Scenario: Cart is emptied after the checkout
        Given I have product "Sig Sauer P226" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "Free" shipping method and "Cash on Delivery" payment
        When I confirm my order
        Then my cart should be empty

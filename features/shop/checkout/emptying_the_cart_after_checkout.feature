@checkout
Feature: Emptying the cart after checkout
    In order to start a new order after purchase
    As a Visitor
    I want my cart to be cleared after I place an order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Sig Sauer P226" priced at "$499.99"
        And the store ships everywhere for Free
        And the store allows paying with "Cash on Delivery"

    @no-api @ui
    Scenario: Cart is emptied after the checkout
        Given I added product "Sig Sauer P226" to the cart
        And I am at the checkout addressing step
        When I specify the email as "jon.snow@example.com"
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I proceed with "Free" shipping method and "Cash on Delivery" payment
        And I confirm my order
        Then my cart should be empty

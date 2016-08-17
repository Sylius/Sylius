@checkout
Feature: Seeing order currency on order summary page
    In order be certain what is the order currency
    As a Customer
    I want to be able to see order currency on the order summary page

    Background:
        Given the store operates on a single channel in "France"
        And that channel allows to shop using the "USD" currency
        And the store has a product "Stark T-Shirt" priced at "$21.50"
        And the store ships everywhere for free
        And the store allows paying offline
        And I am a logged in customer

    @todo
    Scenario: Seeing order currency on the order summary page
        Given I have product "Stark T-Shirt" in the cart
        When I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "France" for "Jon Snow"
        And I proceed order with "Free" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And my order's currency should be "EUR"

    @todo
    Scenario: Seeing order currency on the order summary page after change channel currency
        Given I have product "Stark T-Shirt" in the cart
        When I change my currency to "USD"
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "France" for "Jon Snow"
        And I proceed order with "Free" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And my order's currency should be "USD"

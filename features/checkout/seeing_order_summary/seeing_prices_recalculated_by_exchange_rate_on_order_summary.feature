@checkout
Feature: Seeing prices recalculated by exchange rate on order summary
    In order to be certain about order cost in a selected currency
    As a Customer
    I want to see my order summary with prices recalculated by an exchange rate

    Background:
        Given the store ships to "British Virgin Islands"
        And the store has a zone "English" with code "EN"
        And this zone has the "British Virgin Islands" country member
        And the store operates on a channel named "Web"
        And that channel allows to shop using the "USD" currency
        And that channel allows to shop using the "GBP" currency with exchange rate 3.0
        And that channel uses the "USD" currency by default
        And the store has a product "Lannister Coat" priced at "$100.00"
        And the store has "DHL" shipping method with "$20.00" fee within the "EN" zone
        And the store allows paying offline
        And I am a logged in customer

    @ui
    Scenario: Seeing prices recalculated by an exchange rate from given currency
        When I change my currency to "British Pound"
        And I add product "Lannister Coat" to the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "British Virgin Islands" for "Jon Snow"
        And I proceed with "DHL" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And the "Lannister Coat" product should have unit price "£300.00"
        And my order shipping should be "£60"
        And my order total should be "£360"

    @ui
    Scenario: Seeing prices recalculated by an exchange rate equal to existed in time when order was placed
        When I change my currency to "British Pound"
        And I add product "Lannister Coat" to the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "British Virgin Islands" for "Jon Snow"
        And I proceed with "DHL" shipping method and "Offline" payment
        And the exchange rate for currency "British Pound" was changed to 2.0
        Then I should be on the checkout summary step
        And the "Lannister Coat" product should have unit price "£300.00"
        And my order shipping should be "£60"
        And my order total should be "£360"

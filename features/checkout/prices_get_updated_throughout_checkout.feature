@checkout
Feature: Prices get updated when exchange rate changes during the whole checkout
    In order to keep in sync with current exchange rate throughout checkout
    As a Customer
    I want all prices to be updated after each step of the checkout process

    Background:
        Given the store operates on a single channel in "United States"
        And that channel allows to shop using the "GBP" currency
        And the store has a product "The Pug Mug" priced at "$10.00"
        And the store has "Pigeon Mail" shipping method with "$5.00" fee
        And the store allows paying offline
        And I am a logged in customer
        And I have product "The Pug Mug" in the cart
        And I switch to the "GBP" currency

    @ui
    Scenario: Prices get updated on checkout's start
        When the exchange rate of "US Dollar" to "British Pound" is 2.0
        And I am at the checkout addressing step
        Then the subtotal of "The Pug Mug" item should be "£20.00"

    @ui
    Scenario: Prices get updated after the addressing step
        Given I am at the checkout addressing step
        When the exchange rate of "US Dollar" to "British Pound" is 5.0
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        Then the subtotal of "The Pug Mug" item should be "£50.00"

    @ui
    Scenario: Prices get updated on readdressing
        Given I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        When the exchange rate of "US Dollar" to "British Pound" is 3.0
        And I decide to change my address
        Then the subtotal of "The Pug Mug" item should be "£30.00"

    @ui
    Scenario: Prices get updated after the select shipping step
        Given I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I have selected "Pigeon Mail" shipping method
        When the exchange rate of "US Dollar" to "British Pound" is 2.0
        And I complete the shipping step
        Then the subtotal of "The Pug Mug" item should be "£20.00"

    @ui
    Scenario: Prices get updated on re-selecting shipping step
        Given I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I have selected "Pigeon Mail" shipping method
        And I complete the shipping step
        When the exchange rate of "US Dollar" to "British Pound" is 3.0
        And I decide to change order shipping method
        Then the subtotal of "The Pug Mug" item should be "£30.00"

    @ui
    Scenario: Prices get updated after the select payment method step
        Given I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I have selected "Pigeon Mail" shipping method
        And I complete the shipping step
        And I select "Offline" payment method
        When the exchange rate of "US Dollar" to "British Pound" is 2.0
        And I complete the payment step
        Then the "The Pug Mug" product should have unit price "$10.00"
        And my order shipping should be "$5.00"

    @ui
    Scenario: Prices get updated on re-selecting payment method step
        Given I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "Pigeon Mail" shipping method and "Offline" payment
        When the exchange rate of "US Dollar" to "British Pound" is 3.0
        And I decide to change the payment method
        Then the "The Pug Mug" product should have unit price "£30.00"

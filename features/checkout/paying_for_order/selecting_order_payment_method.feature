@checkout
Feature: Selecting an order payment method
    In order to pay for my order in a convenient way for me
    As a Customer
    I want to be able to choose a payment method

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying with "Paypal Express Checkout"
        And I am a logged in customer

    @ui
    Scenario: Selecting a payment method
        Given I have product "PHP T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I select "Free" shipping method
        And I complete the shipping step
        When I select "Paypal Express Checkout" payment method
        And I complete the payment step

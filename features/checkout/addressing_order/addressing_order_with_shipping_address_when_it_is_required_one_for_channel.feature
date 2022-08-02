@checkout
Feature: Addressing an order with shipping address when it is the required one for the channel
    In order to address an order correctly, but also quickly
    As a Customer
    I want to be able to fill addressing details with only shipping address

    Background:
        Given the store operates on a single channel in "United States"
        And its required address in the checkout is shipping
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And I am a logged in customer
        And I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step

    @api @ui
    Scenario: Addressing an order only with shipping address when it is the required one for the channel
        When I specify the required shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should be on the checkout shipping step

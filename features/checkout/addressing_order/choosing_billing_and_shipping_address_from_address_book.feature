@checkout
Feature: Choosing an address from address book
    In order to address an order by choosing it from my address book
    As a customer
    I want to be able to quick fill in my address information

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for free
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And I am a logged in customer
        And I have an address "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States", "Arkansas" in my address book

    @todo
    Scenario: Choosing shipping address from address book
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I choose "Lucifer Morningstar" for shipping address
        Then I should have this address filled as shipping address

    @todo
    Scenario: Choosing billing address from address book
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I choose "Lucifer Morningstar" for billing address
        Then I should have this address filled as billing address

    @todo
    Scenario: Choosing shipping address from address book and proceed to the next step
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I choose "Lucifer Morningstar" for shipping address
        And I complete the addressing step
        Then I should be on the checkout shipping step

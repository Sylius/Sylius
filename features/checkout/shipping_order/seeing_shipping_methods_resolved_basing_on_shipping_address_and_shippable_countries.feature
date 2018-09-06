@checkout
Feature: Seeing shipping methods resolved basing on shipping address and shippable countries
    In order to select correct shipping method for my order
    As a Customer
    I want to be able to choose only shipping methods that match shipping address and shippable countries of the current channel

    Background:
        Given the store operates on a channel named "Web"
        And the store has a product "Star Trek Ship" priced at "$19.99"
        And the store operates in "United Kingdom" and "United States"
        And the store has a zone "United Kingdom" with code "UK"
        And this zone has the "United Kingdom" country member
        And the channel "Web" has a shippable country "United States"
        And the store has "FedEx" shipping method with "$20.00" fee within the "UK" zone
        And I am a logged in customer

    @ui
    Scenario: Seeing no default shipping method cost if selected country is not shippable
        Given I have product "Star Trek Ship" in the cart
        And I am at the checkout addressing step
        When I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I see the summary of my cart
        Then my cart shipping total should be free

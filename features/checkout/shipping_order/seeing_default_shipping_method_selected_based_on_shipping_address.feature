@checkout
Feature: Seeing default shipping method selected based on shipping address
    In order to select correct shipping method for my order
    As a Customer
    I want to be able to choose only shipping methods that match shipping category of all my items

    Background:
        Given the store operates on a channel named "Web"
        And the store has a product "Star Trek Ship" priced at "$19.99"
        And the store ships to "United Kingdom"
        And the store ships to "United States"
        And the store has a zone "United Kingdom" with code "UK"
        And this zone has the "United Kingdom" country member
        And the store has a zone "United States" with code "US"
        And this zone has the "United States" country member
        And the store has "DHL" shipping method with "$10.00" fee within the "US" zone
        And the store has "FedEx" shipping method with "$20.00" fee within the "UK" zone
        And I am a logged in customer

    @ui
    Scenario: Seeing default shipping method selected based on country from shipping address
        Given I have product "Star Trek Ship" in the cart
        And I am at the checkout addressing step
        When I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should be on the checkout shipping step
        And I should see selected "DHL" shipping method
        And I should not see "FedEx" shipping method

    @ui
    Scenario: Seeing default shipping method selected based on country from shipping address after readdressing
        Given I have product "Star Trek Ship" in the cart
        And I am at the checkout addressing step
        When I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I decide to change my address
        And I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United Kingdom" for "Jon Snow"
        And I complete the addressing step
        Then I should be on the checkout shipping step
        And I should see selected "FedEx" shipping method
        And I should not see "DHL" shipping method

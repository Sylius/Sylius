@checkout
Feature: Seeing shipping methods which category is not same as any category of all my units
    In order to select correct shipping method for my order
    As a Customer
    I want to be able to choose shipping method which category is not same as any category of all my units

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Over-sized" shipping category
        And the store has a product "Star Trek Ship" priced at "$19.99"
        And this product belongs to "Over-sized" shipping category
        And the store has a product "Picasso T-Shirt" priced at "$19.99"
        And the store has "Invisible Post" shipping method with "$30.00" fee
        And this shipping method requires that no units match to "Over-sized" shipping category
        And I am a logged in customer

    @ui
    Scenario: Seeing shipping method which category is not same as category of all my units
        Given I have product "Picasso T-shirt" in the cart
        When I am at the checkout addressing step
        And I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should be on the checkout shipping step
        And I should see "Invisible Post" shipping method

    @ui
    Scenario: Seeing no shipping methods if its category is same as category of all my units
        Given I have product "Star Trek Ship" in the cart
        When I am at the checkout addressing step
        And I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then there should be information about no available shipping methods

    @ui
    Scenario: Seeing no shipping methods if its category is same as one category from my units categories
        Given I have product "Picasso T-shirt" in the cart
        And I have product "Star Trek Ship" in the cart
        When I am at the checkout addressing step
        And I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then there should be information about no available shipping methods

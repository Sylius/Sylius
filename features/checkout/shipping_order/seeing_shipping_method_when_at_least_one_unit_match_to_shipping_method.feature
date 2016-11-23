@checkout
Feature: Seeing shipping methods compatible with categories of units in my cart
    In order to select correct shipping method for my order
    As a Customer
    I want to be able to choose shipping method which based on my units categories

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Over-sized" and "Standard" shipping category
        And the store has a product "Star Trek Ship" priced at "$19.99"
        And this product belongs to "Over-sized" shipping category
        And the store has a product "Picasso T-Shirt" priced at "$19.99"
        And this product belongs to "Standard" shipping category
        And the store has "Raven Post" shipping method with "$10.00" fee
        And this shipping method requires at least one unit matches to "Standard" shipping category
        And the store has "Invisible Post" shipping method with "$30.00" fee
        And this shipping method requires at least one unit matches to "Over-sized" shipping category
        And I am a logged in customer

    @ui
    Scenario: Seeing shipping methods which match to my units categories
        Given I have product "Star Trek Ship" in the cart
        And I have product "Picasso T-Shirt" in the cart
        When I am at the checkout addressing step
        And I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should be on the checkout shipping step
        And I should see "Raven Post" shipping method
        And I should see "Invisible Post" shipping method

    @ui
    Scenario: Not seeing shipping method which not match to my units category
        Given I have product "Star Trek Ship" in the cart
        When I am at the checkout addressing step
        And I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should be on the checkout shipping step
        And I should see "Invisible Post" shipping method
        And I should not see "Raven Post" shipping method

    @ui
    Scenario: Seeing no shipping methods if any of them match to my units categories
        Given the store has a product "Rocket T-shirt" priced at "$20.00"
        And I have product "Rocket T-shirt" in the cart
        When I am at the checkout addressing step
        And I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then there should be information about no available shipping methods

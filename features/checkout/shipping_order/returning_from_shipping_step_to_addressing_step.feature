@checkout
Feature: Returning from shipping step to addressing step
    In order to readdress already addressed order
    As a Customer
    I want to be able to go back to addressing step from shipping step

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Apollo 11 T-Shirt" priced at "$49.99"
        And the store ships everywhere for free
        And I am a logged in customer

    @ui
    Scenario: Going back to addressing step with button
        Given I have product "Apollo 11 T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I decide to change my address
        Then I should be redirected to the addressing step
        And I should be able to go to the shipping step again

    @ui
    Scenario: Going back to the addressing step with steps panel
        Given I have product "Apollo 11 T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I go to the addressing step
        Then I should be redirected to the addressing step
        And I should be able to go to the shipping step again

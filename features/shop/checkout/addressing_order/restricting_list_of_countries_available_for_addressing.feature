@checkout
Feature: Restricting list of countries available for addressing
    In order to make choosing countries easier
    As a Customer
    I want to have only available countries listed

    Background:
        Given the store operates on a single channel in "United States"
        And the store operates in "Poland"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free
        And I am a logged in customer

    @ui
    Scenario: Having only countries available for current channel listed
        Given this channel operates in the "United States" country
        When I add product "PHP T-Shirt" to the cart
        And I go to the checkout addressing step
        Then I should have only "United States" country available to choose from

    @ui
    Scenario: Having all the countries listed if channel does not define available ones
        Given this channel does not define operating countries
        When I add product "PHP T-Shirt" to the cart
        And I go to the checkout addressing step
        Then I should have both "United States" and "Poland" countries available to choose from

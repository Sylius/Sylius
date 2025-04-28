@checkout
Feature: Returning from addressing step to shop homepage
    In order to return to shopping after checkout started
    As a Visitor
    I want to be able to go back to shop homepage from checkout addressing step

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "The Stick of Truth" priced at "$19.99"
        And the store ships everywhere for Free

    @no-api @ui
    Scenario: Returning to shop from addressing step
        When I added product "The Stick of Truth" to the cart
        And I am at the checkout addressing step
        When I go back to store
        Then I should be redirected to the homepage

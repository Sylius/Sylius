@checkout
Feature: Prevent starting checkout with empty cart
    In order to get back to shop after starting checkout with empty cart
    As a Customer
    I want to be able to add something to cart before entering checkout

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying offline
        And I am a logged in customer

    @ui
    Scenario: Being on shop home page after starting checkout with empty cart
        Given I have tried to open checkout addressing page
        Then I should be redirected to the homepage

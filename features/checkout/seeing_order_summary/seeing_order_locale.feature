@checkout
Feature: Seeing order locale on order summary page
    In order be certain what is the order locale
    As a Customer
    I want to be able to see order locale on the order summary page

    Background:
        Given the store operates on a single channel in "United States"
        And that channel allows to shop using "English (United States)" and "French (France)" locales
        And the store has a product "Stark T-Shirt" priced at "$21.50"
        And the store ships everywhere for free
        And the store allows paying offline
        And I am a logged in customer

    @ui
    Scenario: Seeing order locale on the order summary page
        Given I have product "Stark T-Shirt" in the cart
        When I proceed through checkout process
        Then I should be on the checkout summary step
        And my order's locale should be "English (United States)"

    @ui
    Scenario: Seeing order locale on the order summary page after change channel locale
        Given I have product "Stark T-Shirt" in the cart
        When I proceed through checkout process in the "French (France)" locale
        Then I should be on the checkout summary step
        And my order's locale should be "français (France)"

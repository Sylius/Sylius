@checkout
Feature: Picking up the cart with the locale other than the default
    In order to make shopping the most convenient way
    As a Customer
    I want to be able to make shopping in the most preferable language from available

    Background:
        Given the store operates on a single channel in "United States"
        And that channel allows to shop using "English (United States)" and "French (France)" locales
        And I am a logged in customer

    @api
    Scenario: Picking up the cart with the locale other than default
        When I pick up cart in the "French (France)" locale
        And I check details of my cart
        Then my cart's locale should be "French (France)"

    @api @no-ui
    Scenario: Picking up the cart with non valid locale
        When I pick up cart using wrong locale
        Then I should be notified that locale does not exist

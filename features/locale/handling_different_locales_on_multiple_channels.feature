@locales
Feature: Handling different locales on multiple channels
    In order to see right prices
    As a Customer
    I want to browse channels with a valid locale only

    Background:
        Given the store operates on a channel named "Web"
        And that channel allows to shop using "English (United States)", "Polish (Poland)" and "Norwegian (Norway)" locales
        And it uses the "English (United States)" locale by default
        And the store operates on another channel named "Mobile"
        And that channel allows to shop using "Polish (Poland)" and "Norwegian (Norway)" locales
        And it uses the "Polish (Poland)" locale by default

    @ui
    Scenario: Showing locales only from the current channel
        When I browse the "Mobile" channel
        Then I should shop using the "Polish (Poland)" locale
        And I should be able to shop using the "Norwegian (Norway)" locale
        And I should not be able to shop using the "English (United States)" locale

    @ui
    Scenario: Browsing channels using their default locales
        When I browse the "Web" channel
        And I start browsing the "Mobile" channel
        Then I should shop using the "Polish (Poland)" locale

    @ui
    Scenario: Switching a locale applies only to the current channel
        When I browse the "Web" channel
        And I switch to the "Norwegian (Norway)" locale
        And I start browsing the "Mobile" channel
        Then I should still shop using the "Polish (Poland)" locale

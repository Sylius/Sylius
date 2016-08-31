@locales
Feature: Handling disabled locales
    In order to see right prices
    As a Customer
    I want to browse channels with a valid locale only

    Background:
        Given the store operates on a channel named "Web"
        And that channel allows to shop using "English (United States)", "Polish (Poland)" and "Norwegian (Norway)" locales
        And it uses the "English (United States)" locale by default

    @ui
    Scenario: Not showing the disabled locale
        Given the locale "Norwegian (Norway)" is disabled
        When I browse that channel
        Then I should not be able to shop using the "Norwegian (Norway)" locale

    @ui
    Scenario: Failing to browse channel with disabled default locale
        Given the locale "English (United States)" is disabled as well
        When I try to browse that channel
        Then I should not be able to shop without default locale

    @ui
    Scenario: Falling back to the default locale if selected one is not available
        Given I am browsing that channel
        And I switch to the "Polish (Poland)" locale
        When the locale "Polish (Poland)" gets disabled
        Then I should shop using the "English (United States)" locale
        And I should not be able to shop using the "Polish (Poland)" locale

    @ui
    Scenario: Browsing a channel with the default locale disabled while using the other one
        Given I am browsing that channel
        And I switch to the "Polish (Poland)" locale
        When the locale "English (United States)" gets disabled
        Then I should still shop using the "Polish (Poland)" locale
        And I should not be able to shop using the "English (United States)" locale

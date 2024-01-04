@locales
Feature: Switching the current locale
    In order to browse shop in my preferred locale
    As a Customer
    I want to be able to switch locales

    Background:
        Given the store operates on a channel named "Web" with hostname "web"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And it uses the "English (United States)" locale by default

    @ui @api
    Scenario: Showing the current locale
        When I browse that channel
        Then I should shop using the "English (United States)" locale

    @ui @api
    Scenario: Showing available locales
        When I browse that channel
        Then I should be able to shop using the "Polish (Poland)" locale

    @ui @api
    Scenario: Switching the current locale
        When I browse that channel
        And I switch to the "Polish (Poland)" locale
        Then I should shop using the "Polish (Poland)" locale
        And I should be able to shop using the "English (United States)" locale

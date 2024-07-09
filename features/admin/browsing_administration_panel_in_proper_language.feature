@admin_locale
Feature: Browsing administration panel in a proper locale
    In order to see panel in correct language
    As an Admin
    I want to browse admin panel in my locale

    Background:
        Given the store operates on a single channel
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And it uses the "Polish (Poland)" locale by default
        And I am logged in as an administrator
        And I am using "Polish (Poland)" locale for my panel

    @todo-api @ui
    Scenario: Getting errors in my language
        Given I am editing my details
        When I change its email to "wrong-email"
        And I save my changes
        Then I should be notified that this email is not valid in "Polish (Poland)" locale

    @no-api @ui
    Scenario: Seeing menu in my language
        When I open administration dashboard
        Then I should be viewing the administration panel in "Polish (Poland)" locale

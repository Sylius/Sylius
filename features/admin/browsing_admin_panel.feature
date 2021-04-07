@admin_locale
Feature: Handling admin panel
    In order to see panel in correct language
    As an Admin
    I want to browse admin panel in my locale

    Background:
        Given the store operates on a single channel
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And it uses the "English (United States)" locale by default
        And I am logged in as an administrator
        And I am using "English (United States)" locale for my panel

    @ui
    Scenario: Changing my preferred locale
        Given I am editing my details
        When I change its email to "test"
        And I save my changes
        Then I should be notified that this email is not valid

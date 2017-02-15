@admin_locale
Feature: Switching locales on admin's panel
    In order to see the panel in my preferred language
    As an Administrator
    I want to be able to switch locales

    Background:
        Given the store operates on a single channel
        And that channel allows to shop using "English (United States)" and "Spanish (Mexico)" locales
        And it uses the "English (United States)" locale by default
        And I am logged in as an administrator
        And I am using "English (United States)" locale for my panel

    @ui
    Scenario: Changing my preferred locale
        Given I am editing my details
        When I set my locale to "Spanish (Mexico)"
        Then I should be viewing the administration panel in "Spanish (Mexico)"

    @ui
    Scenario: Changing my preferred language to a locale that does not exist in the store
        Given the locale "French (France)" does not exist in the store
        And I am editing my details
        When I set my locale to "French (France)"
        Then I should be viewing the administration panel in "French (France)"

    @ui
    Scenario: Changing panel's locale doesn't change shop's locale
        Given I am editing my details
        When I set my locale to "Spanish (Mexico)"
        And I browse that channel
        Then I should still shop using the "English (United States)" locale

    @ui
    Scenario: Changing shop's locale has doesn't affect admin panel's locale
        Given I switched the shop's locale to "Spanish (Mexico)"
        When I open administration dashboard
        Then I should still be viewing the administration panel in "English (United States)"

    @ui
    Scenario: Locales are saved per each admin's preference
        Given I am using "Spanish (Mexico)" locale for my panel
        And there is an administrator "admin@example.com" identified by "sylius"
        And this administrator is using "French (France)" locale
        When this administrator logs in using "sylius" password
        Then they should be viewing the administration panel in "French (France)"

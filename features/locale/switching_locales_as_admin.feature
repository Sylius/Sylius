@locales
Feature: Switching locales on admin's panel
    In order to see the panel in my preferred language
    As an Administrator
    I want to be able to switch locales

    Background:
        Given the store operates on a single channel
        And that channel allows to shop using "English (United States)", "Spanish (Mexico)" and "French (Canada)" locales
        But the locale "French (Canada)" is disabled
        And it uses the "English (United States)" locale by default
        And I am logged in as an administrator

    @ui @todo
    Scenario: Viewing the administrator's panel in the default locale
        When I open administration dashboard
        Then I should be viewing it in "English (United States)" locale

    @ui @todo
    Scenario: Changing the panel's locale
        When I open administration dashboard
        And I switch to the "Spanish (Mexico)" locale
        Then I should be viewing the panel in "Spanish (Mexico)"

    @ui @todo
    Scenario: Admin's panel can be viewed in a disabled locale
        When I open administration dashboard
        And I switch to the "French (Canada)" locale
        Then I should be viewing the panel in "French (Canada)"

    @ui @todo
    Scenario: Changing panel's locale doesn't change shop's locale
        Given I am using "Spanish (Mexico)" locale for my panel
        When I visit this channel's homepage
        Then I should shop using the "English (United States)" locale

    @ui @todo
    Scenario: Changing shop's locale has doesn't affect admin panel's locale
        Given I browse that channel
        When I switch to the "Spanish (Mexico)" locale
        And I open administration dashboard
        Then I should still be viewing the panel in "English (United States)" locale

    @ui @todo
    Scenario: Every admin has the default locale set at first
        Given there is an administrator "admin@example.com" identified by "sylius"
        When he logs in
        Then he should be viewing the panel in "English (United States)"

    @ui @todo
    Scenario: Locales are saved per each admin's preference
        Given I am using "Spanish (Mexico)" locale for my panel
        And there is an administrator "admin@example.com" identified by "sylius"
        And he is using "French (Canada)" locale
        When he logs in
        Then he should be viewing the panel in "French (Canada)"
        But my panel should still be in "Spanish (Mexico)"

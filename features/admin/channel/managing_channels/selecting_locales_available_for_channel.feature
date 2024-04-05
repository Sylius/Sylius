@managing_channels
Feature: Selecting available locales for a channel
    In order to present stores website in different languages
    As an Administrator
    I want to be able to select available locales

    Background:
        Given the store has currency "Euro"
        And the store has locale "English (United States)"
        And I am logged in as an administrator

    @api @ui
    Scenario: Adding a new channel with locales
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I name it "Mobile Channel"
        And I make it available in "English (United States)"
        And I choose "Euro" as the base currency
        And I choose "English (United States)" as a default locale
        And I select the "Order items based" as tax calculation strategy
        And I add it
        Then I should be notified that it has been successfully created
        And the channel "Mobile Channel" should be available in "English (United States)"

    @api @ui
    Scenario: Adding locales to an existing channel
        Given the store operates on a channel named "Web Channel"
        When I want to modify this channel
        And I make it available in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the channel "Web Channel" should be available in "English (United States)"

    @api @ui
    Scenario: Being unable to disable locale used as the default one for a channel
        Given the store operates on a channel named "Web"
        And this channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And this channel uses the "English (United States)" locale as default
        When I want to modify this channel
        And I make it available only in "Polish (Poland)"
        And I try to save my changes
        Then I should be notified that the default locale has to be enabled

    @api @ui
    Scenario: Being unable to set disabled locale as a default one for a channel
        Given the store has locale "Polish (Poland)"
        And the store operates on a channel named "Web"
        And this channel allows to shop using the "English (United States)" locale
        And this channel uses the "English (United States)" locale as default
        When I want to modify this channel
        And I choose "Polish (Poland)" as a default locale
        And I try to save my changes
        Then I should be notified that the default locale has to be enabled

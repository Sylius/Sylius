@managing_channels
Feature: Adding a new channel
    In order to sell through multiple websites or mobile applications
    As an Administrator
    I want to add a new channel to the registry

    Background:
        Given the store has currency "Euro"
        And the store has locale "English (United States)"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new channel
        Given I want to create a new channel
        When I specify its code as "MOBILE"
        And I name it "Mobile channel"
        And I choose "Euro" as the base currency
        And I choose "English (United States)" as a default locale
        And I add it
        Then I should be notified that it has been successfully created
        And the channel "Mobile channel" should appear in the registry

    @ui
    Scenario: Adding a new channel with additional fields
        Given I want to create a new channel
        When I specify its code as "MOBILE"
        And I name it "Mobile channel"
        And I describe it as "Main distribution channel for mobile apps"
        And I set its hostname as "m.avengers-gear.com"
        And I set its contact email as "contact@avengers-gear.com"
        And I define its color as "blue"
        And I choose "Euro" as the base currency
        And I choose "English (United States)" as a default locale
        And I allow to skip shipping step if only one shipping method is available
        And I allow to skip payment step if only one payment method is available
        And I add it
        Then I should be notified that it has been successfully created
        And the channel "Mobile channel" should appear in the registry

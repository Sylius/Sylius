@managing_channels
Feature: Adding a new channel
    In order to sell through multiple websites or mobile applications
    As an Administrator
    I want to add a new channel to the registry

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Adding a new channel
        Given I want to create a new channel
        When I specify its code as "MOBILE"
        And I name it "Mobile channel"
        And I add it
        Then I should be notified that it has been successfully created
        And the channel "Mobile channel" should appear in the registry

    @ui
    Scenario: Adding a new channel with addtional fields
        Given I want to create a new channel
        When I specify its code as "MOBILE"
        And I name it "Mobile channel"
        And I describe it as "Main distribution channel for mobile apps"
        And I set its hostname as "m.avengers-gear.com"
        And I define its color as "blue"
        And I add it
        Then I should be notified that it has been successfully created
        And the channel "Mobile channel" should appear in the registry

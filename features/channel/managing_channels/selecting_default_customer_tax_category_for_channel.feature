@managing_channels
Feature: Selecting default customer tax category for a channel
    In order to give an opportunity of choosing different default customer taxes logic for different channels
    As an Administrator
    I want to be able to select default customer tax category

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a customer tax category "General"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new channel with default customer tax category
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I name it "Mobile store"
        And I select the "General" as default customer tax category
        And I choose "USD" as the base currency
        And I choose "English (United States)" as a default locale
        And I add it
        Then I should be notified that it has been successfully created
        And the default customer tax category for the "Mobile store" channel should be "General"

    @ui
    Scenario: Selecting default customer tax category for existing channel
        Given the store operates on a channel named "Web store"
        When I want to modify this channel
        And I select the "General" as default customer tax category
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the default customer tax category for the "Web store" channel should be "General"

    @ui
    Scenario: Removing existing channel default customer tax category
        Given the store operates on a channel named "Web store"
        And its default tax zone is zone "US"
        When I want to modify this channel
        And I remove its default customer tax category
        And I save my changes
        Then I should be notified that it has been successfully edited
        And channel "Web store" should not have default customer tax category

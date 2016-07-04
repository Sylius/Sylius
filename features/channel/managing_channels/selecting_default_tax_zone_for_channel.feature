@managing_channels
Feature: Selecting default tax zone for a channel
    In order to give an opportunity of choosing different default taxes logic for different channels
    As an Administrator
    I want to be able to select default tax zone

    Background:
        Given there is a zone "EU" containing all members of the European Union
        And there is a zone "The Rest of the World" containing all other countries
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new channel with default tax zone
        Given I want to create a new channel
        When I specify its code as "MOBILE"
        And I name it "Mobile store"
        And I select the "European Union" as default tax zone
        And I add it
        Then I should be notified that it has been successfully created
        And the default tax zone for the "Mobile store" channel should be "European Union"

    @ui
    Scenario: Selecting default tax zone for existing channel
        Given the store operates on a channel named "Web store"
        And I want to modify this channel
        When I select the "European Union" as default tax zone
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the default tax zone for the "Web store" channel should be "European Union"

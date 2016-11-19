@managing_channels
Feature: Not being able to add a disabled channel when no other exist
    In order to allow users to access to my store
    As an Administrator
    I want to be prevented from adding disabled channel when no other exist

    Background:
        Given the store has currency "Euro"
        And the store has locale "English (United States)"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new disabled channel should result
        Given I want to create a new channel
        When I specify its code as "MOBILE"
        And I name it "Mobile channel"
        And I choose "Euro" as the base currency
        And I choose "English (United States)" as a default locale
        And I disable it
        And I add it
        Then I should be notified that at least one channel has to be defined
        And channel with name "Mobile channel" should not be added

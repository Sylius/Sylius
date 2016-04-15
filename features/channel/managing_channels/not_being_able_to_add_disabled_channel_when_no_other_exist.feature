@managing_channels
Feature: Not being able to add a disabled channel when no other exist
    In order to allow users to access to my store
    As an Administrator
    I want to be prevented from adding disabled channel when no other exist

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Adding a new disabled channel should result
        Given I want to create a new channel
        When I specify its code as "MOBILE"
        And I name it "Mobile channel"
        And I disable it
        And I add it
        Then I should be notified that at least one channel has to be defined is required
        And channel with name "Mobile channel" should not be added

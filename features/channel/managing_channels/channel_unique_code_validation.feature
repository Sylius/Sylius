@managing_channels
Feature: Channel unique code validation
    In order to uniquely identify channels
    As an Administrator
    I want to be prevented from adding two channels with same code

    Background:
        Given the store operates on a channel identified by "WEB" code
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add channel with taken code
        Given I want to create a new channel
        When I specify its code as "WEB"
        And I name it "Mobile channel"
        And I try to add it
        Then I should be notified that channel with this code already exists
        And there should still be only one channel with code "WEB"

@managing_channels
Feature: Channel validation
    In order to avoid making mistakes when managing a channel
    As an Administrator
    I want to be prevented from adding or editing it without specifying required fields

    Background:
        Given I am logged in as an administrator

    @api @ui
    Scenario: Trying to add a new channel without specifying its code
        When I want to create a new channel
        And I name it "Mobile channel"
        But I do not specify its code
        And I try to add it
        Then I should be notified that code is required
        And channel with name "Mobile channel" should not be added

    @api @ui
    Scenario: Trying to add a new channel without specifying its name
        When I want to create a new channel
        And I specify its code as "MOBILE"
        But I do not name it
        And I try to add it
        Then I should be notified that name is required
        And channel with code "MOBILE" should not be added

    @api @ui
    Scenario: Trying to add a new channel without base currency
        When I want to create a new channel
        And I specify its code as "MOBILE"
        But I do not choose base currency
        And I try to add it
        Then I should be notified that base currency is required
        And channel with code "MOBILE" should not be added

    @api @ui
    Scenario: Trying to add a new channel without default locale
        When I want to create a new channel
        And I specify its code as "MOBILE"
        But I do not choose default locale
        And I try to add it
        Then I should be notified that default locale is required
        And channel with code "MOBILE" should not be added

    @api @ui
    Scenario: Trying to remove name from existing channel
        Given the store operates on a channel named "Web Channel"
        When I want to modify this channel
        And I remove its name
        And I try to save my changes
        Then I should be notified that name is required
        And this channel should still be named "Web Channel"

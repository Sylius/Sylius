@managing_channels
Feature: Channel validation
    In order to avoid making mistakes when managing a channel
    As an Administrator
    I want to be prevented from adding or editing it without specifying required fields

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Trying to add a new channel without specifying its code
        Given I want to create a new channel
        When I name it "Mobile channel"
        But I do not specify its code
        And I try to add it
        Then I should be notified that code is required
        And channel with name "Mobile channel" should not be added

    @ui
    Scenario: Trying to add a new channel without specifying its name
        Given I want to create a new channel
        When I specify its code as "MOBILE"
        But I do not name it
        And I try to add it
        Then I should be notified that name is required
        And channel with code "MOBILE" should not be added

    @ui
    Scenario: Trying to add a new channel without base currency
        Given I want to create a new channel
        When I specify its code as "MOBILE"
        But I do not choose base currency
        And I try to add it
        Then I should be notified that base currency is required
        And channel with code "MOBILE" should not be added

    @ui
    Scenario: Trying to add a new channel without default locale
        Given I want to create a new channel
        When I specify its code as "MOBILE"
        But I do not choose default locale
        And I try to add it
        Then I should be notified that default locale is required
        And channel with code "MOBILE" should not be added

    @ui
    Scenario: Trying to remove name from existing channel
        Given the store operates on a channel named "Web Channel"
        And I want to modify this channel
        When I remove its name
        And I try to save my changes
        Then I should be notified that name is required
        And this channel should still be named "Web Channel"

    @ui @javascript
    Scenario: Trying to add a channel with duplicate shipping countries
        Given the store operates in "United States"
        And the store has locale "English (United States)"
        And the store has currency "US Dollar"
        And I want to create a new channel
        When I specify its code as "MOBILE"
        And I name it "Web Channel"
        And I choose "English (United States)" as a default locale
        And I choose "US Dollar" as the base currency
        And I add a shippable country "United States"
        And I add a shippable country "United States"
        And I try to add it
        Then I should be notified that shippable countries must be unique
        And channel with code "MOBILE" should not be added

    @ui @javascript
    Scenario: Trying to add a channel with empty shipping countries
        Given the store operates in "United States"
        And the store has locale "English (United States)"
        And the store has currency "US Dollar"
        And I want to create a new channel
        When I specify its code as "MOBILE"
        And I name it "Web Channel"
        And I choose "English (United States)" as a default locale
        And I choose "US Dollar" as the base currency
        And I add a shippable country but I don't select one
        And I try to add it
        Then I should be notified that shippable countries cannot be blank
        And channel with code "MOBILE" should not be added
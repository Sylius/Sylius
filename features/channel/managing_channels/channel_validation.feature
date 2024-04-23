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
    Scenario: Trying to add a new channel with a too long name
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I specify its name as a too long string
        And I try to add it
        Then I should be notified that name is too long
        And channel with code "MOBILE" should not be added

    @api @ui
    Scenario: Trying to add a new channel with a too long code
        When I want to create a new channel
        And I name it "Mobile channel"
        And I specify a too long code
        And I try to add it
        Then I should be notified that code is too long

    @api @ui
    Scenario: Trying to add a new channel without specifying its name
        When I want to create a new channel
        And I specify its code as "MOBILE"
        But I do not name it
        And I try to add it
        Then I should be notified that name is required
        And channel with code "MOBILE" should not be added

    @api @ui
    Scenario: Trying to add a new channel with too long color
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I specify its color as a too long string
        And I try to add it
        Then I should be notified that color is too long
        And channel with code "MOBILE" should not be added

    @api @ui
    Scenario: Trying to add a new channel with too long hostname
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I specify its hostname as a too long string
        And I try to add it
        Then I should be notified that hostname is too long
        And channel with code "MOBILE" should not be added

    @api @no-ui
    Scenario: Trying to add a new channel with too long theme name
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I specify its theme name as a too long string
        And I try to add it
        Then I should be notified that "theme name" is too long
        And channel with code "MOBILE" should not be added

    @api @no-ui
    Scenario: Trying to add a new channel with too long tax calculation strategy
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I specify its tax calculation strategy as a too long string
        And I try to add it
        Then I should be notified that "tax calculation strategy" is too long
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
    Scenario: Trying to add a new channel with too long contact email
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I specify its contact email as a too long string
        And I try to add it
        Then I should be notified that "contact email" is too long
        And channel with code "MOBILE" should not be added

    @api @ui
    Scenario: Trying to add a new channel with too long contact phone number
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I specify its contact phone number as a too long string
        And I try to add it
        Then I should be notified that "contact phone number" is too long
        And channel with code "MOBILE" should not be added

    @api @ui
    Scenario: Trying to remove name from existing channel
        Given the store operates on a channel named "Web Channel"
        When I want to modify this channel
        And I remove its name
        And I try to save my changes
        Then I should be notified that name is required
        And this channel should still be named "Web Channel"

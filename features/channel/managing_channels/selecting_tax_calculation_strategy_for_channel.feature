@managing_channels
Feature: Selecting tax calculation strategy for a channel
    In order to use different tax strategies on different channels
    As an Administrator
    I want to be able to select tax calculation strategy

    Background:
        Given the store has currency "Euro"
        And the store has locale "English (United States)"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new channel with implicitly selected tax calculation strategy
        Given I want to create a new channel
        When I specify its code as "MOBILE"
        And I name it "Mobile store"
        And I choose "Euro" as the base currency
        And I choose "English (United States)" as a default locale
        And I add it
        Then I should be notified that it has been successfully created
        And the tax calculation strategy for the "Mobile store" channel should be "Order items based"

    @ui
    Scenario: Adding a new channel with tax calculation strategy
        Given I want to create a new channel
        When I specify its code as "MOBILE"
        And I select the "Order item units based" as tax calculation strategy
        And I name it "Mobile store"
        And I choose "Euro" as the base currency
        And I choose "English (United States)" as a default locale
        And I add it
        Then I should be notified that it has been successfully created
        And the tax calculation strategy for the "Mobile store" channel should be "Order item units based"

    @ui
    Scenario: Changing tax calculation strategy of existing channel
        Given the store operates on a channel named "Web store"
        And I want to modify this channel
        When I select the "Order item units based" as tax calculation strategy
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the tax calculation strategy for the "Web store" channel should be "Order item units based"

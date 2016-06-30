@managing_channels
Feature: Selecting default tax calculation strategy for a channel
    In order to use different tax strategies on different channels
    As an Administrator
    I want to be able to select default tax calculation strategy

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Adding a new channel with implicitly selected default tax calculation strategy
        Given I want to create a new channel
        When I specify its code as MOBILE
        And I name it "Mobile store"
        And I add it
        Then I should be notified that it has been successfully created
        And "Order items based" should be default tax calculation strategy for the "Mobile store" channel

    @ui
    Scenario: Adding a new channel with default tax calculation strategy
        Given I want to create a new channel
        When I specify its code as MOBILE
        And I select the "Order item units based" as default tax calculation strategy
        And I name it "Mobile store"
        And I add it
        Then I should be notified that it has been successfully created
        And "Order item units based" should be default tax calculation strategy for the "Mobile store" channel

    @ui
    Scenario: Changing default tax calculation strategy of existing channel
        Given the store operates on a channel named "Web store"
        And I want to modify this channel
        When I select the "Order item units based" as default tax calculation strategy
        And I save my changes
        Then I should be notified that it has been successfully edited
        And "Order item units based" should be default tax calculation strategy for the "Web store" channel

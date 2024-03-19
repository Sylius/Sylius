@managing_channels
Feature: Selecting available currencies for a channel
    In order to give possibility to pay in different currencies on my stores website
    As an Administrator
    I want to be able to select available currencies

    Background:
        Given the store has currency "Euro"
        And the store has locale "English (United States)"
        And the store operates in "United States"
        And I am logged in as an administrator

    @api @ui
    Scenario: Adding a new channel with currencies
        When I want to create a new channel
        And I specify its code as MOBILE
        And I choose "Euro" as the base currency
        And I name it "Mobile store"
        And I allow for paying in "Euro"
        And I make it available in "English (United States)"
        And I choose "English (United States)" as a default locale
        And I select the "Order items based" as tax calculation strategy
        And I add it
        Then I should be notified that it has been successfully created
        And paying in Euro should be possible for the "Mobile store" channel

    @api @ui
    Scenario: Adding currencies to an existing channel
        Given the store operates on a channel named "Web store"
        When I want to modify this channel
        And I allow for paying in "Euro"
        And I choose "English (United States)" as a default locale
        And I save my changes
        Then I should be notified that it has been successfully edited
        And paying in Euro should be possible for the "Web store" channel

@managing_channels
Feature: Choosing whether to show the lowest product price or not while creating a channel
    In order to show the lowest price before the product has been discounted only for certain channels
    As an Administrator
    I want to add a new channel that has this feature enabled or not

    Background:
        Given the store has currency "Euro"
        And the store has locale "English (United States)"
        And the store operates in "United States" and "Poland"
        And I am logged in as an administrator

    @api @ui
    Scenario: Adding a new channel with lowest price before the product has been discounted enabled by default
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I name it "Mobile"
        And I choose "Euro" as the base currency
        And I make it available in "English (United States)"
        And I choose "English (United States)" as a default locale
        And I select the "Order items based" as tax calculation strategy
        And I add it
        Then I should be notified that it has been successfully created
        And the "Mobile" channel should have the lowest price of discounted products prior to the current discount enabled

    @api @ui
    Scenario: Adding a new channel with lowest price before the product has been discounted enabled
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I name it "Mobile"
        And I choose "Euro" as the base currency
        And I make it available in "English (United States)"
        And I choose "English (United States)" as a default locale
        And I select the "Order items based" as tax calculation strategy
        And I enable showing the lowest price of discounted products
        And I add it
        Then I should be notified that it has been successfully created
        And the "Mobile" channel should have the lowest price of discounted products prior to the current discount enabled

    @api @ui
    Scenario: Adding a new channel with lowest price before the product has been discounted disabled
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I name it "Mobile"
        And I choose "Euro" as the base currency
        And I make it available in "English (United States)"
        And I choose "English (United States)" as a default locale
        And I select the "Order items based" as tax calculation strategy
        And I disable showing the lowest price of discounted products
        And I add it
        Then I should be notified that it has been successfully created
        And the "Mobile" channel should have the lowest price of discounted products prior to the current discount disabled

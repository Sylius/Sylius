@managing_channels
Feature: Choosing a required address in the checkout for a channel
    In order to give an opportunity of choosing different required address in the checkout for different channels
    As an Administrator
    I want to be able to choose a required address

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @api @ui
    Scenario: Adding a new channel with a required address in the checkout
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I name it "Mobile Store"
        And I choose "USD" as the base currency
        And I make it available in "English (United States)"
        And I choose "English (United States)" as a default locale
        And I select the "Order items based" as tax calculation strategy
        And I choose shipping address as a required address in the checkout
        And I add it
        Then I should be notified that it has been successfully created
        And the required address in the checkout for this channel should be shipping

    @api @ui
    Scenario: Changing a required address in the checkout for an existing channel
        Given the store operates on a channel named "Web Store"
        When I want to modify this channel
        And I choose shipping address as a required address in the checkout
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the required address in the checkout for this channel should be shipping

@managing_channels
Feature: Editing shop billing data on channel
    In order to have proper shop billing data on shop-related documents
    As an Administrator
    I want to be able to edit shop billing data on a channel

    Background:
        Given the store operates on a channel named "Web Store"
        And the store ships to "United States"
        And channel "Web Store" billing data is "Ragnarok", "Pacific Coast Hwy", "90806" "Los Angeles", "United States" with "1100110011" tax ID
        And I am logged in as an administrator

    @ui
    Scenario: Editing shop billing data on channel
        When I want to modify a channel "Web Store"
        And I specify company as "Götterdämmerung"
        And I specify tax ID as "666777"
        And I specify shop billing address as "Valhalla", "123" "Asgard", "United States"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this channel company should be "Götterdämmerung"
        And this channel tax ID should be "666777"
        And this channel shop billing address should be "Valhalla", "123" "Asgard", "United States"

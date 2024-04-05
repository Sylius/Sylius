@managing_channels
Feature: Choosing whether to show the lowest product price or not while editing a channel
    In order to show the lowest price before the product has been discounted only for certain channels
    As an Administrator
    I want to be able to edit channels and enable or disable the lowest price of discounted products on them

    Background:
        Given the store operates on a channel named "EU Channel"
        And the store operates on another channel named "US Channel"
        And the channel "EU Channel" has showing the lowest price of discounted products enabled
        And the channel "US Channel" has showing the lowest price of discounted products disabled
        And I am logged in as an administrator

    @no-api @ui
    Scenario: Enabling showing the lowest price of discounted products on a channel
        When I want to modify a channel "US Channel"
        And I enable showing the lowest price of discounted products
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "US Channel" channel should have the lowest price of discounted products prior to the current discount enabled

    @no-api @ui
    Scenario: Disabling showing the lowest price of discounted products on a channel
        When I want to modify a channel "EU Channel"
        And I disable showing the lowest price of discounted products
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "EU Channel" channel should have the lowest price of discounted products prior to the current discount disabled

    @api @no-ui
    Scenario: Enabling showing the lowest price of discounted products on a channel
        When I want to modify the price history config of channel "US Channel"
        And I change showing of the lowest price of discounted products to be enabled
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "US Channel" channel should have the lowest price of discounted products prior to the current discount enabled

    @api @no-ui
    Scenario: Disabling showing the lowest price of discounted products on a channel
        When I want to modify the price history config of channel "EU Channel"
        And I change showing of the lowest price of discounted products to be disabled
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "EU Channel" channel should have the lowest price of discounted products prior to the current discount disabled

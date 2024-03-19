@managing_channels
Feature: Deleting multiple channels
    In order to remove test, obsolete or incorrect channels in an efficient way
    As an Administrator
    I want to be able to delete multiple channels at once

    Background:
        Given the store operates on a channel named "US Store"
        And the store operates on another channel named "PL Store"
        And the store operates on another channel named "DE Store"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Deleting multiple channels at once
        When I browse channels
        And I check the "PL Store" channel
        And I check also the "DE Store" channel
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see a single channel in the list
        And I should see the channel "US Store" in the list

@managing_channels
Feature: Denying access to shipping methods for unauthorized users
    In order to denies access for unauthorized users
    As a Visitor
    I don't want to access to manage shipping methods

    Background:
        Given the store operates on a channel named "Web Store"
        And the store operates on another channel named "Mobile Store"

    @api
    Scenario: Trying to delete channel
        When I try to delete channel "Mobile Store"
        Then I should be notified that my access has been denied

    @api
    Scenario: Trying to rename the channel
        Given I want to modify a channel "Mobile Store"
        When I rename it to "Other Store"
        And I try to save my changes
        Then I should be notified that my access has been denied

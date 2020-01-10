@managing_channels
Feature: Editing menu taxon on channel
    In order to have proper products' categories displayed on each channel
    As an Administrator
    I want to be able to edit menu taxon on a channel

    Background:
        Given the store operates on a channel named "Web Store"
        And the store ships to "United States"
        And the store classifies its products as "Clothes" and "Guns"
        And channel "Web Store" has menu taxon "Clothes"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Editing menu taxon on the channel
        When I want to modify a channel "Web Store"
        And I change its menu taxon to "Guns"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this channel menu taxon should be "Guns"

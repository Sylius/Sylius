@products_accessibility_in_multiple_channels
Feature: Multi-channel support
    In order to see how my store looks on a different channels
    As a Developer
    I want to change between channels with ease

    Background:
        Given the store operates on a channel named "Poland"
        And there is product "Onion" available in this channel
        And the store operates on another channel named "United States"
        And there is product "Banana" available in that channel

    @ui
    Scenario:
        When I change my current channel to "Poland"
        Then I should be able to access product "Onion"
        But I should not be able to access product "Banana"

    @ui
    Scenario:
        When I change my current channel to "United States"
        Then I should be able to access product "Banana"
        But I should not be able to access product "Onion"

@channels
Feature: Browsing shop on right channel
    In order to shop
    As a Customer
    I want to be redirected to the right channel

    Background:
        Given the store operates on a channel named "Default" in "USD" currency and with hostname "127.0.0.1"
        And the store also operates on a channel named "Alternative" in "USD" currency and with hostname "127.0.0.1"
        And the channel "Default" is disabled

    @ui
    Scenario: Browsing shop in active channel
        When I visit the homepage
        Then I should be on channel "Alternative"

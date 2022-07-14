@channels
Feature: Filtering out disabled channels
    In order to avoid mistakes
    As a Customer
    I want to be able to browse only available shops

    Background:
        Given the store operates on a channel named "Fashion" in "USD" currency and with hostname "127.0.0.1"
        And the store operates on a channel named "Furniture" in "EUR" currency and with hostname "127.0.0.1"
        And the channel "Fashion" is disabled

    @ui
    Scenario: Seeing Furniture shop homepage
        When I visit the homepage
        Then I should see "Furniture" shop

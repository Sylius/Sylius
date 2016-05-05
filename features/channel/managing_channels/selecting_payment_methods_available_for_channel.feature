@managing_channels
Feature: Selecting available payment methods for a channel
    In order to give an opportunity of choosing different payment methods to customers
    As an Administrator
    I want to be able to select available payment methods

    Background:
        Given the store has a payment method "Offline" with a code "OFF"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new channel with payment methods
        Given I want to create a new channel
        When I specify its code as MOBILE
        And I name it "Mobile store"
        And I select the "Offline" payment method
        And I add it
        Then I should be notified that it has been successfully created
        And the "Offline" payment method should be available for the "Mobile store" channel

    @ui
    Scenario: Adding payment methods to an existing channel
        Given the store operates on a channel named "Web store"
        And I want to modify this channel
        When I select the "Offline" payment method
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Offline" payment method should be available for the "Web store" channel

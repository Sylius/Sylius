@managing_channels
Feature: Selecting available shipping methods for a channel
    In order to give an opportunity of choosing different shipping methods to customer
    As an Administrator
    I want to be able to select available shipping methods

    Background:
        Given there is a zone "EU" containing all members of the European Union
        And the store allows shipping with "UPS Carrier" identified by "UPS_CARRIER"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new channel with shipping methods
        Given I want to create a new channel
        When I specify its code as MOBILE
        And I name it "Mobile store"
        And I select the "UPS Carrier" shipping method
        And I add it
        Then I should be notified that it has been successfully created
        And the "UPS Carrier" shipping method should be available for the "Mobile store" channel

    @ui
    Scenario: Adding shipping methods to an existing channel
        Given the store operates on a channel named "Web store"
        And I want to modify this channel
        When I select the "UPS Carrier" shipping method
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "UPS Carrier" shipping method should be available for the "Web store" channel

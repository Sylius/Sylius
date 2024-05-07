@managing_shipping_methods
Feature: Toggling a shipping method
    In order to change shipping methods which are available to my customers
    As an Administrator
    I want to be able to switch state of shipping method between enable and disable

    Background:
        Given the store operates on a single channel in "United States"
        And the store allows shipping with "UPS Carrier" identified by "UPS_CARRIER"
        And I am logged in as an administrator

    @ui @api
    Scenario: Disabling the shipping method
        Given the shipping method "UPS Carrier" is enabled
        When I want to modify this shipping method
        And I disable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this shipping method should be disabled

    @ui @api
    Scenario: Enabling the shipping method
        Given the shipping method "UPS Carrier" is disabled
        When I want to modify this shipping method
        And I enable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this shipping method should be enabled

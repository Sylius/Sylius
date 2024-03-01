@managing_shipping_methods
Feature: Editing shipping method
    In order to change shipping method details
    As an Administrator
    I want to be able to edit a shipping method

    Background:
        Given the store operates on a single channel in "United States"
        And the store is available in "English (United States)"
        And the store allows shipping with "UPS Carrier" identified by "UPS_CARRIER"
        And I am logged in as an administrator

    @ui @api
    Scenario: Seeing disabled code field when editing shipping method
        When I want to modify a shipping method "UPS Carrier"
        Then I should not be able to edit its code

    @ui @api
    Scenario: Renaming the shipping method
        When I want to modify a shipping method "UPS Carrier"
        And I rename it to "UPS Transport" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this shipping method name should be "UPS Transport"

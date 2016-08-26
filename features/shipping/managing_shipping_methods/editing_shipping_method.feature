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

    @todo
    Scenario: Trying to change shipping method code
        Given I want to modify a shipping method "UPS Carrier"
        When I change its code to "UPS"
        And I save my changes
        Then I should be notified that code cannot be changed
        And shipping method "UPS Carrier" should still have code "UPS_CARRIER"

    @ui
    Scenario: Seeing disabled code field when editing shipping method
        When I want to modify a shipping method "UPS Carrier"
        Then the code field should be disabled

    @ui @javascript
    Scenario: Renaming the shipping method
        Given I want to modify a shipping method "UPS Carrier"
        When I rename it to "UPS Transport" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this shipping method name should be "UPS Transport"

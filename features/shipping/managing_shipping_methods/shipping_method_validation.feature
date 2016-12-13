@managing_shipping_methods
Feature: Shipping method validation
    In order to avoid making mistakes when managing a shipping method
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store operates on a single channel in "United States"
        And the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a new shipping method without specifying its code
        When I want to create a new shipping method
        And I name it "FedEx Carrier" in "English (United States)"
        But I do not specify its code
        And I try to add it
        Then I should be notified that code is required
        And shipping method with name "FedEx Carrier" should not be added

    @ui
    Scenario: Trying to add a new shipping method without specifying its name
        Given I want to create a new shipping method
        When I specify its code as "FED_EX"
        But I do not name it
        And I try to add it
        Then I should be notified that name is required
        And shipping method with code "FED_EX" should not be added

    @ui
    Scenario: Trying to add a new shipping method without specifying its zone
        Given the store does not have any zones defined
        When I want to create a new shipping method
        And I name it "FedEx Carrier" in "English (United States)"
        But I do not specify its zone
        And I try to add it
        Then I should be notified that zone has to be selected
        And shipping method with name "Food and Beverage Tax Rates" should not be added

    @ui
    Scenario: Trying to remove name from existing shipping method
        Given the store allows shipping with "UPS Ground"
        And I want to modify this shipping method
        When I remove its name from "English (United States)" translation
        And I try to save my changes
        Then I should be notified that name is required
        And this shipping method should still be named "United States Sales Tax"

    @ui
    Scenario: Trying to remove zone from existing shipping method
        Given the store allows shipping with "UPS Ground"
        And I want to modify this shipping method
        When I remove its zone
        And I try to save my changes
        Then I should be notified that zone has to be selected

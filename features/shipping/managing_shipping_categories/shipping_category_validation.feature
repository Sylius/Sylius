@managing_shipping_categories
Feature: Shipping category validation
    In order to avoid making mistakes when managing a shipping category
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a new shipping category without specifying its code
        When I want to create a new shipping category
        And I name it "Standard"
        But I do not specify its code
        And I try to add it
        Then I should be notified that code is required
        And shipping category with name "Standard" should not be added

    @ui
    Scenario: Trying to add a new shipping category without specifying its name
        When I want to create a new shipping category
        And I specify its code as "STANDARD"
        But I do not specify its name
        And I try to add it
        Then I should be notified that name is required
        And shipping category with name "Standard" should not be added

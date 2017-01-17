@managing_shipping_categories
Feature: Shipping category unique code validation
    In order to uniquely identify shipping categories
    As an Administrator
    I want to be prevented from adding two shipping categories with same code

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Standard" shipping category identified by "STANDARD"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add shipping category with taken code
        Given I want to create a new shipping category
        When I specify its code as "STANDARD"
        And I name it "Normal"
        And I try to add it
        Then I should be notified that shipping category with this code already exists
        And there should still be only one shipping category with code "STANDARD"

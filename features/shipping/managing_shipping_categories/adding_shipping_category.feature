@managing_shipping_categories
Feature: Adding a new shipping category
    In order to deliver goods according to their shipping categories
    As an Administrator
    I want to add a new shipping category to the registry

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new shipping category with detailed information
        Given I want to create a new shipping category
        When I specify its code as "OVER_SIZED"
        And I name it "Over sized"
        And I specify its description as "Shipping method with huge dimension"
        And I add it
        Then I should be notified that it has been successfully created
        And the shipping category "Over sized" should appear in the registry

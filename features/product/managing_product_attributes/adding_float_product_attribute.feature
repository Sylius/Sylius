@managing_product_attributes
Feature: Adding a new float product attribute
    In order to show specific product's parameters to customer
    As an Administrator
    I want to add a new float product attribute

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui @api
    Scenario: Adding a new float product attribute
        When I want to create a new float product attribute
        And I specify its code as "display_size"
        And I name it "Display Size" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the float attribute "Display Size" should appear in the store

    @ui @no-api
    Scenario: Seeing disabled type field while adding a float product attribute
        When I want to create a new float product attribute
        Then the type field should be disabled

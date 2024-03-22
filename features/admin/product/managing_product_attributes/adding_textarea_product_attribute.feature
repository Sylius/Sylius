@managing_product_attributes
Feature: Adding a new textarea product attribute
    In order to show specific product's parameters to customer
    As an Administrator
    I want to add a new textarea product attribute

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui @api
    Scenario: Adding a new textarea product attribute
        When I want to create a new textarea product attribute
        And I specify its code as "t_shirt_details"
        And I name it "T-Shirt details" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the textarea attribute "T-Shirt details" should appear in the store

    @ui @no-api
    Scenario: Seeing disabled type field while adding a textarea product attribute
        When I want to create a new textarea product attribute
        Then the type field should be disabled

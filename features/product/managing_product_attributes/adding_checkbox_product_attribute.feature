@managing_product_attributes
Feature: Adding a new checkbox product attribute
    In order to show specific product's parameters to customer
    As an Administrator
    I want to add a new checkbox product attribute

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new checkbox product attribute
        Given I want to create a new checkbox product attribute
        When I specify its code as "t_shirt_with_cotton"
        And I name it "T-shirt with cotton" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the checkbox attribute "T-shirt with cotton" should appear in the store

    @ui
    Scenario: Seeing disabled type field while adding a checkbox product attribute
        When I want to create a new checkbox product attribute
        Then the type field should be disabled

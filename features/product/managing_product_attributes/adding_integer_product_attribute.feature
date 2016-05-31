@managing_product_attributes
Feature: Adding a new integer product attribute
    In order to show specific product's parameters to customer
    As an Administrator
    I want to add a new integer product attribute

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new integer product attribute
        Given I want to create a new integer product attribute
        When I specify its code as "book_pages"
        And I name it "Book pages" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the integer attribute "Book pages" should appear in the store

    @ui
    Scenario: Seeing disabled type field while adding a integer product attribute
        When I want to create a new integer product attribute
        Then the type field should be disabled

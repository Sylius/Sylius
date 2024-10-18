@managing_product_attributes
Feature: Adding a new date product attribute
    In order to show specific product's parameters to customer
    As an Administrator
    I want to add a new date product attribute

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @api @todo-ui
    Scenario: Adding a new date product attribute
        When I want to create a new date product attribute
        And I specify its code as "expiration_date"
        And I name it "Expiration Date" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the date attribute "Expiration Date" should appear in the store

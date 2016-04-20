@managing_product_attributes
Feature: Adding a new textarea product attribute
    In order to show specific product's parameters to customer
    As an Administrator
    I want to add a new textarea product attribute

    Background:
        Given the store has locale "English (United States)"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new textarea product attribute
        Given I want to create a new textarea product attribute
        When I specify it code as "t_shirt_details"
        And I name it "T-shirt details" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the attribute "T-shirt details" should appear in the store

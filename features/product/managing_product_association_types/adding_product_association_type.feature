@managing_product_association_types
Feature: Adding a new product association type
    In order to connect products together in many contexts
    As an Administrator
    I want to add a new product association type to the store

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new product association type
        When I want to create a new product association type
        And I specify its code as "cross_sell"
        And I name it "Cross sell" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the product association type "Cross sell" should appear in the store

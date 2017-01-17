@managing_product_association_types
Feature: Product association type unique code validation
    In order to uniquely identify product association types
    As an Administrator
    I want to be prevented from adding two product association types with the same code

    Background:
        Given the store is available in "English (United States)"
        And the store has a product association type "Cross sell" with a code "cross_sell"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a new product association type with a taken code
        When I want to create a new product association type
        And I specify its code as "cross_sell"
        And I name it "Cross sell" in "English (United States)"
        And I try to add it
        Then I should be notified that product association type with this code already exists
        And there should still be only one product association type with a code "cross_sell"

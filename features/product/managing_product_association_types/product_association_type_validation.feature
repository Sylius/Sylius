@managing_product_association_types
Feature: Product association type validation
    In order to avoid making mistakes when managing a product association type
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store is available in "English (United States)"
        And the store has a product association type "Cross sell"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a new product association type without specifying its code
        When I want to create a new product association type
        And I name it "Up sell" in "English (United States)"
        But I do not specify its code
        And I try to add it
        Then I should be notified that code is required
        And the product association type with name "Up sell" should not be added

    @ui
    Scenario: Trying to add a new product association type without specifying its name
        When I want to create a new product association type
        And I specify its code as "up_sell"
        But I do not name it
        And I try to add it
        Then I should be notified that name is required
        And the product association type with code "up_sell" should not be added

    @ui
    Scenario: Trying to remove name from an existing product association type
        When I want to modify the "Cross sell" product association type
        And I remove its name from "English (United States)" translation
        And I try to save my changes
        Then I should be notified that name is required
        And this product association type should still be named "Cross sell"

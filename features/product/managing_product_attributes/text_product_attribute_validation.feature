@managing_product_attributes
Feature: Text product attribute validation
    In order to avoid making mistakes when managing text product attributes
    As an Administrator
    I want to be prevented from adding it without specify required fields

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a new text product attribute without name
        When I want to create a new text product attribute
        And I specify its code as "t_shirt_with_cotton"
        But I do not name it
        And I try to add it
        Then I should be notified that name is required
        And the attribute with code "t_shirt_with_cotton" should not appear in the store

    @ui
    Scenario: Trying to add a new text product attribute without code
        When I want to create a new text product attribute
        And I name it "T-shirt brand" in "English (United States)"
        But I do not specify its code
        And I try to add it
        Then I should be notified that code is required
        And the attribute with name "T-shirt brand" should not appear in the store

    @ui
    Scenario: Trying to remove name for existing text product attribute
        Given the store has a text product attribute "T-shirt cotton brand"
        When I want to edit this product attribute
        And I specify its code as "t_shirt_with_cotton"
        And I remove its name from "English (United States)" translation
        And I try to save my changes
        Then I should be notified that name is required
        And the attribute with code "t_shirt_with_cotton" should not appear in the store

    @ui
    Scenario: Trying to add a new text product attribute with wrong configuration
        When I want to create a new text product attribute
        And I name it "T-shirt brand" in "English (United States)"
        And I specify its code as "t_shirt_brand"
        And I specify its min length as 8
        And I specify its max length as 6
        And I try to add it
        Then I should be notified that max length must be greater or equal to the min length
        And the attribute with code "t_shirt_brand" should not appear in the store

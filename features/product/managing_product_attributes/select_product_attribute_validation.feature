@managing_product_attributes
Feature: Select product attribute validation
    In order to avoid making mistakes when managing select product attributes
    As an Administrator
    I want to be prevented from adding it with wrong configuration

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Trying to add a new select product attribute with wrong max entries value
        When I want to create a new select product attribute
        And I name it "Mug material" in "English (United States)"
        And I specify its code as "mug_material"
        And I add value "Banana Skin" in "English (United States)"
        And I also add value "Orange Skin" in "English (United States)"
        And I check multiple option
        And I specify its min entries value as 8
        And I specify its max entries value as 6
        And I try to add it
        Then I should be notified that max entries value must be greater or equal to the min entries value
        And the attribute with code "mug_material" should not appear in the store

    @ui @javascript
    Scenario: Trying to add a new select product attribute with wrong min entries value
        When I want to create a new select product attribute
        And I name it "Mug material" in "English (United States)"
        And I specify its code as "mug_material"
        And I add value "Banana Skin" in "English (United States)"
        And I also add value "Orange Skin" in "English (United States)"
        And I check multiple option
        And I specify its min entries value as 4
        And I specify its max entries value as 6
        And I try to add it
        Then I should be notified that min entries value must be lower or equal to the number of added choices
        And the attribute with code "mug_material" should not appear in the store

    @ui @javascript
    Scenario: Trying to add a new select product attribute with specified entries values but without multiple option
        When I want to create a new select product attribute
        And I name it "Mug material" in "English (United States)"
        And I specify its code as "mug_material"
        And I add value "Banana Skin" in "English (United States)"
        And I also add value "Orange Skin" in "English (United States)"
        And I do not check multiple option
        And I specify its min entries value as 4
        And I specify its max entries value as 6
        And I try to add it
        Then I should be notified that multiple must be true if min or max entries values are specified
        And the attribute with code "mug_material" should not appear in the store

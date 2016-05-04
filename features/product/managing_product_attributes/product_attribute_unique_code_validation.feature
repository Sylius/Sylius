@managing_product_attributes
Feature: Product attribute unique code validation
    In order to uniquely identify product attributes
    As an Administrator
    I want to be prevented from adding a new product attribute with taken code

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a new product attribute with taken code
        Given the store has a text product attribute "T-shirt cotton material" with code "t_shirt_material"
        And I want to create a new text product attribute
        When I specify its code as "t_shirt_material"
        And I name it "T-shirt special material" in "English (United States)"
        And I add it
        Then I should be notified that product attribute with this code already exists
        And there should still be only one product attribute with code "t_shirt_material"

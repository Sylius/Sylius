@managing_products
Feature: Adding a new product with a select attribute
    In order to extend my merchandise with more complex products
    As an Administrator
    I want to add a new product with a select attribute to the shop

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a non-translatable select product attribute "Mug material" with value "Ceramic"
        And I am logged in as an administrator

    @ui @javascript @api
    Scenario: Adding a product with a select attribute with choices in different locales
        When I want to create a new configurable product
        And I specify its code as "mug"
        And I name it "PHP Mug" in "English (United States)"
        And I add the "Mug material" attribute
        And I select "Ceramic" value for the "Mug material" attribute
        And I add it
        Then I should be notified that it has been successfully created
        And the product "PHP Mug" should appear in the store
        And select attribute "Mug material" of product "PHP Mug" should be "Ceramic"

    @api @no-ui
    Scenario: Trying to add an invalid select attribute to product
        When I want to create a new configurable product
        And I specify its code as "mug"
        And I name it "PHP Mug" in "English (United States)"
        And I add the "Mug material" attribute
        And I set the invalid string value of the non-translatable "Mug material" attribute to "ceramic"
        And I add it
        Then I should be notified that the value of the "Mug material" attribute has invalid type
        And product with code "mug" should not be added

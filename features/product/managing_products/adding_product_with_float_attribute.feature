@managing_products
Feature: Adding a new product with a float attribute
    In order to extend my merchandise with more complex products
    As an Administrator
    I want to add a new product with a float attribute to the shop

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a non-translatable float product attribute "Display Size"
        And I am logged in as an administrator

    @ui @javascript @api
    Scenario: Adding a float attribute to a product
        When I want to create a new configurable product
        And I specify its code as "display_size"
        And I name it "Smartphone" in "English (United States)"
        And I set its non-translatable "Display Size" attribute to 12.5
        And I add it
        Then I should be notified that it has been successfully created
        And the product "Smartphone" should appear in the store
        And non-translatable attribute "Display Size" of product "Smartphone" should be 12.5

    @api @no-ui
    Scenario: Trying to add an invalid float attribute to product
        When I want to create a new configurable product
        And I specify its code as "44_MAGNUM"
        And I name it "44 Magnum" in "English (United States)"
        And I set the invalid string value of the non-translatable "Display Size" attribute to "12.5"
        And I try to add it
        Then I should be notified that the value of the "Display Size" attribute has invalid type
        And product with code "44_MAGNUM" should not be added

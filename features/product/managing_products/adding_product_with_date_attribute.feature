@managing_products
Feature: Adding a new product with a date attribute
    In order to extend my merchandise with more complex products
    As an Administrator
    I want to add a new product with a date attribute to the shop

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a non-translatable date product attribute "Manufactured" with format "Y-m-d"
        And I am logged in as an administrator

    @api
    Scenario: Adding a date attribute to a product
        When I want to create a new configurable product
        And I specify its code as "mug"
        And I name it "Mug" in "English (United States)"
        And I set its non-translatable "Manufactured" attribute to "2023-10-10"
        And I add it
        Then I should be notified that it has been successfully created
        And the product "Mug" should appear in the store
        And non-translatable attribute "Manufactured" of product "Mug" should be "2023-10-10"

    @api @no-ui
    Scenario: Trying to add an invalid date attribute to product
        When I want to create a new configurable product
        And I specify its code as "44_MAGNUM"
        And I name it "44 Magnum" in "English (United States)"
        And I set the invalid integer value of the non-translatable "Manufactured" attribute to 10
        And I try to add it
        Then I should be notified that the value of the "Manufactured" attribute has invalid type
        And product with code "44_MAGNUM" should not be added

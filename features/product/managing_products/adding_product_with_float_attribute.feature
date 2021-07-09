@managing_products
Feature: Adding a new product with an float attribute
    In order to extend my merchandise with more complex products
    As an Administrator
    I want to add a new product with an float attribute to the shop

    Background:
        Given the store operates on a single channel in "United States"
        And the store has an float product attribute "Display Size"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding an float attribute to product
        Given I want to create a new simple product
        When I specify its code as "display_size"
        And I name it "Smartphone" in "English (United States)"
        And I set its price to "$100.00" for "United States" channel
        And I set its "Display Size" attribute to "12.5" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the product "Smartphone" should appear in the store
        And attribute "Display Size" of product "Smartphone" should be "12.5"

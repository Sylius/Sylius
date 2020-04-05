@managing_products
Feature: Select taxon for an existing product
    In order to specify in which taxons a product is available
    As an Administrator
    I want to be able to select taxons for an existing product

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "T-Shirts", "Accessories", "Funny" and "Sad"
        And the store has a "T-Shirt Banana" configurable product
        And the store has a product "T-Shirt Batman"
        And I am logged in as an administrator
        And I am using "English (United Kingdom)" locale for my panel

    @ui @javascript
    Scenario: Specifying main taxon for configurable product
        Given I want to modify the "T-Shirt Banana" product
        When I choose main taxon "T-Shirts"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product main taxon should be "T-Shirts"

    @ui @javascript
    Scenario: Specifying main taxon for simple product
        Given I want to modify the "T-Shirt Batman" product
        When I choose main taxon "Sad"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product main taxon should be "Sad"


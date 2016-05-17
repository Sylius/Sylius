@managing_products
Feature: Select taxon for a product
    In order to specify in which taxons a product is available
    As an Administrator
    I want to be able to select taxons for a product

    Background:
        Given the store is available in "English (United States)"
        And the store classifies its products as "T-Shirts", "Accessories", "Funny" and "Sad"
        And the store has "T-shirt Banana" product
        And I am logged in as an administrator

    @todo @javascript
    Scenario: Selecting a main taxon of product
        Given I want to modify a "T-shirt Banana" product
        When I select main taxon "T-shirts"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product main taxon should be "T-shirts"

    @todo @javascript
    Scenario: Selecting taxons for product
        Given I want to modify a "T-shirt Banana" product
        When I select taxon "T-shirts"
        And I select taxon "Funny"
        Then I should be notified that it has been successfully edited
        And this product taxon should be "T-shirts"
        And this product taxon should be "Funny"

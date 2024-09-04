@managing_product_attributes
Feature: Filtering product attributes
    In order to quickly find specific product attributes
    As an Administrator
    I want to filter product attributes by various criteria

    Background:
        Given the store has a text product attribute "T-Shirt brand" with code "t_shirt_brand_abc"
        And the store has a checkbox product attribute "T-Shirt with cotton" with code "t_shirt_with_cotton_xyz"
        And this product attribute is not translatable
        And the store has a integer product attribute "Book pages" with code "book_pages_xyz"
        And I am logged in as an administrator
        And I am browsing product attributes

    @api @ui
    Scenario: Filtering product attributes by code
        When I search by "xyz" code
        Then I should see 2 product attributes in the list
        And I should see the product attribute "T-Shirt with cotton" in the list
        And I should also see the product attribute "Book pages" in the list

    @api @ui
    Scenario: Filtering product attributes by name
        When I search by "T-Shirt" name
        Then I should see 2 product attributes in the list
        And I should see the product attribute "T-Shirt brand" in the list
        And I should also see the product attribute "T-Shirt with cotton" in the list

    @api @ui
    Scenario: Filtering product attributes by type
        When I choose "checkbox" in the type filter
        And I filter
        Then I should see a single product attribute in the list
        And I should see the product attribute "T-Shirt with cotton" in the list

    @api @ui
    Scenario: Filtering product attributes by multiple types
        When I choose "text" and "integer" in the type filter
        And I filter
        Then I should see 2 product attributes in the list
        And I should see the product attribute "T-Shirt brand" in the list
        And I should also see the product attribute "Book pages" in the list

    @api @ui
    Scenario: Filtering translatable product attributes
        When I choose "Yes" in the translatable filter
        And I filter
        Then I should see 2 product attributes in the list
        And I should see the product attribute "T-Shirt brand" in the list
        And I should see the product attribute "Book pages" in the list

    @api @ui
    Scenario: Filtering non-translatable product attributes
        When I choose "No" in the translatable filter
        And I filter
        Then I should see a single product attribute in the list
        And I should see the product attribute "T-Shirt with cotton" in the list

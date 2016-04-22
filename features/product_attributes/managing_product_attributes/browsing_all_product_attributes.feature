@managing_product_attributes
Feature: Browsing product attributes
    In order to see all product attributes available in the store
    As an Administrator
    I want to browse product attributes

    Background:
        Given the store has a text product attribute "T-shirt brand" with code "t_shirt_brand"
        And the store has a checkbox product attribute "T-shirt with cotton" with code "t_shirt_with_cotton"
        And the store has a integer product attribute "Book pages" with code "book_pages"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing all product attributes in store
        When I want to see all product attributes in store
        Then I should see 3 product attributes in the list
        And I should see the product attribute "T-shirt brand" in the list

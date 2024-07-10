@accessing_edit_page_from_product_show_page
Feature: Accessing the product edit page from the show page
    In order to edit product in the simple way
    As an Administrator
    I want to be able to move to edit page directly from product show page

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Iron shield" configurable product
        And the product "Iron shield" has "Iron shield - very big" variant with code "123456789-xl"
        And the product "Iron shield" has "Iron shield - very small" variant with code "123456789-xs"
        And I am logged in as an administrator
        And I am browsing products

    @ui @no-api
    Scenario: Accessing to product edit page from product show page
        When I access the "Iron shield" product
        And I go to edit page
        Then I should be on "Iron shield" product edit page

    @ui @no-api
    Scenario: Accessing to variant edit page from product show page
        When I access the "Iron shield" product
        And I go to edit page of "Iron shield - very big" variant
        Then I should be on "Iron shield - very big" variant edit page

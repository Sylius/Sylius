@accessing_edit_page_from_product_show_page
Feature: Accessing the product edit page from the show page
    In order to edit product in the simple way
    As an Administrator
    I want to be able to move to edit page directly from product show page

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Iron sword" product
        And the store has a "Iron shield" configurable product
        And the product "Iron shield" has "Iron shield - very big" variant with code "123456789-xl"
        And the product "Iron shield" has "Iron shield - very small" variant with code "123456789-xs"
        And I am logged in as an administrator

    @no-api @ui
    Scenario: Accessing to product edit page from simple product show page
        When I access the "Iron sword" product
        And I go to edit page
        Then I should be on "Iron sword" product edit page

    @no-api @ui
    Scenario: Accessing to product edit page from configurable product show page
        When I access the "Iron shield" product
        And I go to edit page
        Then I should be on "Iron shield" product edit page

    @no-api @ui
    Scenario: Accessing to variant edit page from product show page
        When I access the "Iron shield" product
        And I go to edit page of "Iron shield - very big" variant
        Then I should be on "Iron shield - very big" variant edit page

    @no-api @ui
    Scenario: Accessing to product show page from simple product edit page
        When I want to modify the "Iron sword" product
        And I go to show page
        Then I should be on "Iron sword" product show page

    @no-api @ui
    Scenario: Accessing to product show page from configurable product edit page
        When I want to modify the "Iron shield" product
        And I go to show page
        Then I should be on "Iron shield" product show page

    @no-api @ui
    Scenario: Not being able to access product show page from simple product create page
        When I want to create a new simple product
        Then I should not be able to open the product show page

    @no-api @ui
    Scenario: Not being able to access product show page from configurable product create page
        When I want to create a new configurable product
        Then I should not be able to open the product show page

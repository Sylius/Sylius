@accessing_price_history
Feature: Accessing the price history from the simple product show page
    In order to check the price history of a simple product
    As an Administrator
    I want to be able to access the price history's page for the given channel from a simple product show page

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Ursus C-355" priced at "$1,000.00" in "United States" channel
        And there is a catalog promotion with "company_bankruptcy_sale" code and "Company bankruptcy sale" name
        And the catalog promotion "Company bankruptcy sale" is available in "United States"
        And it applies on "Ursus C-355" product
        And it reduces price by "90%"
        And it is enabled
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Being able to access price history from simple product show page
        Given I am browsing products
        When I access the "Ursus C-355" product
        And I access the price history of a simple product for "United States" channel
        Then I should see 2 log entries in the catalog price history

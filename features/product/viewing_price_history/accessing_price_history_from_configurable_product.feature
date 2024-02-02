@accessing_price_history
Feature: Accessing the price history from the configurable product show page
    In order to check the price history of an product variant
    As an Administrator
    I want to be able to access the price history's page for the given channel from a configurable product show page

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And the product "Wyborowa Vodka" has a "Wyborowa Vodka Exquisite" variant priced at "$40.00" and originally priced at "$15.00"
        And the product "Wyborowa Vodka" has a "Wyborowa Vodka Lemon" variant priced at "$10.00"
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Being able to access the price history of variant from the configurable product show page
        Given I am browsing products
        When I access the "Wyborowa Vodka" product
        And I access the price history of a product variant "Wyborowa Vodka Exquisite" for "United States" channel
        Then I should see 1 log entries in the catalog price history

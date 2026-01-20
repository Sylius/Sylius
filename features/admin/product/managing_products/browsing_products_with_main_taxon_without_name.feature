@managing_products
Feature: Browsing products with main taxon that has no name in the current locale
    In order to browse products in the admin panel
    As an Administrator
    I want to be able to browse products even when their main taxon has no name translation

    Background:
        Given the store operates on a single channel in "Dutch (Netherlands)" locale
        And the store classifies its products as "T-Shirts"
        And the store has a "T-Shirt" product
        And the product "T-Shirt" has a main taxon "T-Shirts"
        And the "T-Shirts" taxon has an empty name in the "Dutch (Netherlands)" locale
        And I am logged in as an administrator

    @ui
    Scenario: Browsing products when main taxon has no name in admin locale should not cause an error
        Given I am using "Dutch (Netherlands)" locale for my panel
        When I browse products
        Then I should see the product "T-Shirt" in the list

@viewing_products
Feature: Viewing enabled products only
    In order to see only available products
    As a Customer
    I want to see only enabled products

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store classifies its products as "T-Shirts"
        And the store has a product "Super Cool T-Shirt" priced at "$4.00" belonging to the "T-Shirts" taxon
        And the store has a product "PHP T-Shirt" priced at "$13.00" belonging to the "T-Shirts" taxon
        And the store has a product "Shiny T-Shirt" priced at "$2.00" belonging to the "T-Shirts" taxon
        And the "PHP T-Shirt" product is disabled

    @api @ui
    Scenario: Seeing only enabled products
        When I browse products from product taxon code "T-Shirts"
        Then I should see 2 products in the list
        And I should not see the product "PHP T-Shirt"

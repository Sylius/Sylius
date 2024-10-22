@managing_products
Feature: Filtering listed products from a taxon
    In order to quickly find products from a specific category
    As an Administrator
    I want to filter listed products from a taxon

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Jeans" and "T-Shirts"
        And the store has a product "Old t-shirt" belonging to the "T-Shirts" taxon
        And the store has a product "Small jeans" belonging to the "Jeans" taxon
        And the store has a product "Big jeans" belonging to the "Jeans" taxon
        And I am logged in as an administrator

    @api @no-ui
    Scenario: Filtering listed products by taxon
        When I am browsing products from "T-Shirts" taxon
        Then I should see the "T-Shirts" taxon
        But I should not see the "Jeans" taxon

    @api @ui
    Scenario: Filtering listed products by product
        When I am browsing products from "Jeans" taxon
        And I filter them by "Small jeans" product
        Then I should see the "Small jeans" product
        But I should not see the "Big jeans" product

@viewing_products
Feature: Accessing a disabled taxon
    In order to have a good navigation
    As a Visitor
    I want to be informed that a taxon is not available

    Background:
        Given the store operates on a single channel in "United States"

    @ui @no-api
    Scenario: Accessing a disabled taxon
        Given the store has "T-Shirts" taxonomy
        And the "T-Shirts" taxon is disabled
        When I try to browse products from taxon "T-Shirts"
        Then I should be informed that the taxon does not exist

    @api @no-ui
    Scenario: Filtering products by taxon available only for enabled taxon
        Given the store has "Food" taxonomy
        Given the store has a product "Baguette" priced at "$2.00" belonging to the "Food" taxon
        And the "Food" taxon is disabled
        When I browse products from product taxon code "Food"
        Then I should see empty list of products

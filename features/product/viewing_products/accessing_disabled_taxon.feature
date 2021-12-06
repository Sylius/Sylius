@viewing_products
Feature: Accessing a disabled taxon
    In order to have a good navigation
    As a Visitor
    I want to be informed that a taxon is not available

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Accessing a disabled taxon
        Given the store has "T-Shirts" taxonomy
        And the "T-Shirts" taxon is disabled
        When I try to browse products from taxon "T-Shirts"
        Then I should be informed that the taxon does not exist

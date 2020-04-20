@viewing_products
Feature: Viewving children taxons of current taxon on the sidebar
    In order to be able to easily browse children taxons
    As a Visitor
    I want to see the children taxon list of current taxon on the sidebar

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Category"
        And the "Category" taxon has child taxon "Clothes"
        And the "Clothes" taxon has children taxons "T-Shirts", "Coats" and "Trousers"
        And channel "United States" has menu taxon "Category"

    @ui
    Scenario: Viewing only enabled taxons in the vertical menu
        Given the "Coats" taxon is disabled
        When I try to browse products from taxon "Clothes"
        Then I should not see "Coats" in the vertical menu
        And I should see "T-Shirts" and "Trousers" in the vertical menu

    @ui
    Scenario: Cannot go to parent disabled taxon from the vertical menu
        Given the "Clothes" taxon is disabled
        When I try to browse products from taxon "T-Shirts"
        Then I should not see go level up link in the vertical menu

@homepage
Feature: Viewing only enabled taxons in taxon menu
    In order to not access disabled taxons
    As a Visitor
    I want to see only enabled taxons on taxon menu

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Category"
        And the "Category" taxon has children taxons "Clothes" and "Accessories"
        And the "Clothes" taxon has children taxons "T-Shirts" and "Coats"
        And the "Accessories" taxon has children taxons "Caps" and "Belts"
        And channel "United States" has menu taxon "Category"

    @ui @api
    Scenario: Viewing only enabled taxons in taxon menu
        Given the "Clothes" taxon is disabled
        And the "Belts" taxon is disabled
        When I check available taxons
        Then I should see "Caps" in the menu
        And I should not see "T-Shirts", "Coats" and "Belts" in the menu

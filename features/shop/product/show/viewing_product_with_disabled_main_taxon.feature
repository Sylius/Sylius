@viewing_products
Feature: Viewing product with a disabled main taxon
    In order to only navigate to available taxons
    As a Visitor
    I want to have clickable links in the breadcrumb

    Background:
        Given the store operates on a channel named "Poland"
        And the store classifies its products as "T-Shirts"
        And the "T-Shirts" taxon has children taxon "Men" and "Women"
        And the store has a product "T-Shirt Coconut" available in "Poland" channel
        And this product has a main taxon "Men"

    @ui @no-api
    Scenario: Seeing the breadcrumb with a disabled main taxon
        Given the "Men" taxon is disabled
        When I view product "T-Shirt Coconut"
        Then I should not be able to click disabled main taxon "Men" in the breadcrumb

@viewing_products
Feature: Searching products by name within a specific taxon
    In order to browse products which names contains specific text
    As a Visitor
    I want to be able to search products by name within a specific taxon

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Guns"
        And the store has a product "44 Magnum" available in "United States" channel
        And this product belongs to "Guns"
        And the store has a product "Glock 17" available in "United States" channel
        And this product belongs to "Guns"

    @ui
    Scenario: Searching for products with name containing specific text
        When I browse products from taxon "Guns"
        And I search for product with name "Magnum"
        Then I should see the product "44 Magnum"
        And I should not see the product "Glock 17"

    @ui
    Scenario: Clearing filters
        When I browse products from taxon "Guns"
        And I search for product with name "Magnum"
        And I clear filter
        Then I should see the product "44 Magnum"
        And I should see the product "Glock 17"

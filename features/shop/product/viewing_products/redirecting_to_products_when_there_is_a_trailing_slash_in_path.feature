@viewing_products
Feature: Redirecting to products when there is a trailing slash in path
    In order to be redirected on products list
    As a Visitor
    I want to be redirected on products list when path has a trailing slash

    Background:
        Given the store has currency "Euro"
        And the store operates on a channel named "Poland"
        And the store classifies its products as "T-Shirts", "Funny" and "Sad"
        And the store has a product "T-Shirt Banana" available in "Poland" channel
        And this product belongs to "T-Shirts"
        And the store has a product "Plastic Tomato" available in "Poland" channel
        And this product belongs to "Funny"

    @ui @no-api
    Scenario: Redirecting to products when there is a trailing slash in path
        When I try to browse products from taxon "T-Shirts" with a trailing slash in the path
        Then I should be redirected on the product list from taxon "T-Shirts"
        And I should see the product "T-Shirt Banana"
        But I should not see the product "Plastic Tomato"

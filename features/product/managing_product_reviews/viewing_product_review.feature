@managing_product_reviews
Feature: Viewing a product review
    In order to see product's reviews
    As a visitor
    I want to be able to see product's reviews

    Background:
        Given the store is available in "English (United States)"

    @ui
    Scenario: Viewing a detailed page with product's review
        Given the store has a product "T-shirt banana"
        And this product has a review "My review"
        When I check this product's details
        Then I should see the product review "My review"

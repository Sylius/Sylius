@managing_product_reviews
Feature: Adding a new product review
    In order to see product's reviews
    As a visitor
    I want to add a new product review

    Background:
        Given the store is available in "English (United States)"
        And the store has a product "T-shirt banana"

    @ui
    Scenario: Adding a new product review
        Given I want to create a new review of this product
        When I specify its rating to "5"
        And I set its email as "customer@test.us"
        And I set its title as "My review"
        And I set its comment as "This product rocks"
        And I add it
        Then I should be notified that it has been successfully created
        And the review "My review" of the "T-shirt banana" product should appear in product details page

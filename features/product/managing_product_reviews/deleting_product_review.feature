@managing_product_reviews
Feature: Deleting product reviews
    In order to remove test, obsolete or incorrect product reviews
    As an Administrator
    I want to be able to delete a product review

    Background:
        Given the store has customer "Mike Ross" with email "ross@teammike.com"
        And the store has a product "Lamborghini Gallardo Model"
        And this product has a review titled "Awesome" and rated 5 with a comment "Nice product" added by customer "ross@teammike.com"
        And I am logged in as an administrator

    @ui
    Scenario: Deleting a product review
        When I delete the "Awesome" product review
        Then I should be notified that it has been successfully deleted
        And this product review should no longer exist in the registry

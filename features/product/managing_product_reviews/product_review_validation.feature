@managing_product_reviews
Feature: Product review validation
    In order to avoid making mistakes when managing a product review
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store has customer "Mike Ross" with email "ross@teammike.com"
        And the store has a product "Lamborghini Gallardo Model"
        And this product has a review titled "Awesome" and rated 4 with a comment "Nice product" added by customer "ross@teammike.com"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to remove title from an existing product review
        When I want to modify the "Awesome" product review
        And I remove its title
        And I try to save my changes
        Then I should be notified that title is required
        And this product review should still be titled "Awesome"

    @ui
    Scenario: Trying to remove comment from an existing product review
        When I want to modify the "Awesome" product review
        And I remove its comment
        And I try to save my changes
        Then I should be notified that comment is required
        And this product review should still have a comment "Nice product"

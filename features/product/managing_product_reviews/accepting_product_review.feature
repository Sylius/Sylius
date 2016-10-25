@managing_product_reviews
Feature: Accepting a product review
    In order to give the customers insight into others opinions about a product
    As an Administrator
    I want to be able to accept a product review

    Background:
        Given the store has customer "Mike Ross" with email "ross@teammike.com"
        And the store has a product "Lamborghini Gallardo Model"
        And this product has a new review titled "Awesome" and rated 4 added by customer "ross@teammike.com"
        And I am logged in as an administrator

    @ui
    Scenario: Accepting a product review
        When I want to browse product reviews
        And I accept the "Awesome" product review
        Then I should be notified that it has been successfully accepted
        And this product review status should be "accepted"

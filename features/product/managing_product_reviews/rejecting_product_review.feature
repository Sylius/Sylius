@managing_product_reviews
Feature: Rejecting a product review
    In order to have control over the opinions about my products
    As an Administrator
    I want to be able to reject a product review

    Background:
        Given the store has customer "Mike Ross" with email "ross@teammike.com"
        And the store has a product "Lamborghini Gallardo Model"
        And this product has a new review titled "Crap" and rated 1 added by customer "ross@teammike.com"
        And I am logged in as an administrator

    @ui
    Scenario: Rejecting a product review
        When I want to browse product reviews
        And I reject the "Crap" product review
        Then I should be notified that it has been successfully rejected
        And this product review status should be "rejected"

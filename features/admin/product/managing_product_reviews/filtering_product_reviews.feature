@managing_product_reviews
Feature: Browsing product reviews
    In order to work with multiple reviews
    As an Administrator
    I want to filter product reviews

    Background:
        Given the store has customer "Mike Ross" with email "ross@teammike.com"
        And the store has a product "Lamborghini Gallardo Model"
        And this product has a review titled "Awesome" and rated 5 with a comment "Nice product" added by customer "ross@teammike.com"
        And this product has a new review titled "Bad" and rated 1 added by customer "ross@teammike.com"
        And I am logged in as an administrator

    @ui @api
    Scenario: Browsing accepted reviews
        When I want to browse product reviews
        And I choose "accepted" as a status filter
        And I filter
        Then I should see a single product review in the list
        And I should see the product review "Awesome" in the list

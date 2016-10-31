@managing_product_reviews
Feature: Browsing product reviews
    In order to know customer's opinion about my products
    As an Administrator
    I want to browse product reviews

    Background:
        Given the store has customer "Mike Ross" with email "ross@teammike.com"
        And the store has a product "Lamborghini Gallardo Model"
        And this product has a review titled "Awesome" and rated 5 with a comment "Nice product" added by customer "ross@teammike.com"
        And this product has also a review titled "Bad" and rated 1 with a comment "Really bad" added by customer "ross@teammike.com"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing all product reviews in store
        When I want to browse product reviews
        Then I should see 2 reviews in the list
        And I should see the product review "Awesome" in the list
        And I should also see the product review "Bad" in the list

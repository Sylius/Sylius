@managing_product_reviews
Feature: Recalculating product average rating
    In order to have my product's average rating properly calculated
    As an Administrator
    I want to have product's average rating recalculated after review rate change
    
    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Lamborghini Gallardo Model"
        And this product has a review titled "Awesome" and rated 4 with a comment "Nice product" added by customer "ross@teammike.com"
        And this product has a review titled "Not bad" and rated 3 with a comment "Not bad car" added by customer "specter@teamharvey.com"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Product's average rating is correctly recalculated after review's rate change
        When I want to modify the "Awesome" product review
        And I choose 5 as its rating
        And I save my changes
        Then I should be notified that it has been successfully edited
        And average rating of product "Lamborghini Gallardo Model" should be 4

    @ui
    Scenario: Product's average rating is correctly recalculated after review's rate change
        When I delete the "Awesome" product review
        Then I should be notified that it has been successfully deleted
        And average rating of product "Lamborghini Gallardo Model" should be 3

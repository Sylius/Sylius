@managing_product_reviews
Feature: Editing product reviews
    In order to correct customer's opinion about my products
    As an Administrator
    I want to edit a product review

    Background:
        Given there is a customer "Mike Ross" with an email "ross@teammike.com" and a password "thePassword"
        And the store has a product "Lamborghini Gallardo Model"
        And this product has a review titled "Awesome" and rated 4 with a comment "Nice product" added by customer "ross@teammike.com"
        And I am logged in as an administrator

    @ui @api
    Scenario: Changing a title of a product review
        Given this product has a review titled "Bewilderig" and rated 5 with a comment "Nice product" added by customer "ross@teammike.com"
        When I want to modify the "Bewilderig" product review
        And I change its title to "Bewildering"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product review title should be "Bewildering"

    @ui @api
    Scenario: Changing a comment of a product review
        Given this product has a review titled "Bewildering" and rated 5 with a comment "Nice prodct" added by customer "ross@teammike.com"
        When I want to modify the "Bewildering" product review
        And I change its comment to "Nice product"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product review comment should be "Nice product"

    @ui @javascript @api
    Scenario: Changing a rating of a product review
        When I want to modify the "Awesome" product review
        And I choose 5 as its rating
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product review rating should be 5

    @ui @no-api
    Scenario: Seeing a product's name while editing a product review
        When I want to modify the "Awesome" product review
        Then I should be editing review of product "Lamborghini Gallardo Model"

    @ui @no-api
    Scenario: Seeing a customer's name while editing a product review
        When I want to modify the "Awesome" product review
        Then I should see the customer's name "Mike Ross"

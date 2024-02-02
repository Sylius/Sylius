@managing_product_reviews
Feature: Deleting multiple product reviews
    In order to remove test, obsolete or incorrect product reviews in an efficient way
    As an Administrator
    I want to be able to delete multiple product reviews at once

    Background:
        Given the store has a product "Audi RS7 model"
        And this product has a review titled "Awesome" and rated 5 with a comment "Nice product" added by customer "batman@dc.com"
        And this product has also a review titled "Bad" and rated 1 with a comment "Really bad" added by customer "theflash@dc.com"
        And this product has also a review titled "Cool" and rated 4 with a comment "Quite cool" added by customer "aquaman@dc.com"
        And I am logged in as an administrator

    @ui @mink:chromedriver @no-api
    Scenario: Deleting multiple product reviews at once
        When I browse product reviews
        And I check the "Awesome" product review
        And I check also the "Bad" product review
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see a single product review in the list
        And I should see the product review "Cool" in the list

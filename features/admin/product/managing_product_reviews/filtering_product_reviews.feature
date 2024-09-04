@managing_product_reviews
Feature: Filtering product reviews
    In order to quickly find product reviews
    As an Administrator
    I want to be able to filter product reviews in the list

    Background:
        Given the store has a product "PHP Book"
        And this product has a review titled "Awesome" and rated 5 with a comment "Nice product" added by customer "john@example.com"
        And the store has a product "Symfony Book"
        And this product has a new review titled "Great book" and rated 4 added by customer "tom@example.com"
        And I am logged in as an administrator
        And I am browsing product reviews

    @api @ui
    Scenario: Browsing accepted reviews
        When I choose "accepted" as a status filter
        And I filter
        Then I should see a single product review in the list
        And I should see the product review "Awesome" in the list

    @api @ui
    Scenario: Filtering product reviews by title
        When I filter with title containing "Great"
        Then I should see a single product review in the list
        And I should see the product review "Great book" in the list

    @api @ui @mink:chromedriver
    Scenario: Filtering product reviews by product
        When I filter by "PHP Book" product
        Then I should see a single product review in the list
        And I should see the product review "Awesome" in the list

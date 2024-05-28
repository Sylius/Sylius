@managing_product_reviews
Feature: Sorting product reviews
    In order to change the order by which product reviews are displayed
    As an Administrator
    I want to be able to sort product reviews in the list

    Background:
        Given the store has a product "PHP Book"
        And this product has a new review titled "Awesome" and rated 5 added by customer "john@example.com", created 6 days ago
        And this product has an accepted review titled "Not bad" and rated 3 added by customer "joe@example.com", created 4 days ago
        And this product has a rejected review titled "Great book" and rated 4 added by customer "tom@example.com", created 2 days ago
        And I am logged in as an administrator
        And I am browsing product reviews

    @ui @todo-api
    Scenario: Displaying product reviews sorted by date in descending order by default
        Then I should see 3 reviews in the list
        And the first product review in the list should have title "Great book"
        And the last product review in the list should have title "Awesome"

    @ui @todo-api
    Scenario: Sorting product reviews ascending by date
        When I sort the product reviews ascending by date
        Then I should see 3 reviews in the list
        And the first product review in the list should have title "Awesome"
        And the last product review in the list should have title "Great book"

    @ui @todo-api
    Scenario: Sorting product reviews ascending by title
        When I sort the product reviews ascending by title
        Then I should see 3 reviews in the list
        And the first product review in the list should have title "Awesome"
        And the last product review in the list should have title "Not bad"

    @ui @todo-api
    Scenario: Sorting product reviews descending by title
        When I sort the product reviews descending by title
        Then I should see 3 reviews in the list
        And the first product review in the list should have title "Not bad"
        And the last product review in the list should have title "Awesome"

    @ui @todo-api
    Scenario: Sorting product reviews ascending by rating
        When I sort the product reviews ascending by rating
        Then I should see 3 reviews in the list
        And the first product review in the list should have title "Not bad"
        And the last product review in the list should have title "Awesome"

    @ui @todo-api
    Scenario: Sorting product reviews descending by rating
        When I sort the product reviews descending by rating
        Then I should see 3 reviews in the list
        And the first product review in the list should have title "Awesome"
        And the last product review in the list should have title "Not bad"

    @ui @todo-api
    Scenario: Sorting product reviews ascending by status
        When I sort the product reviews ascending by status
        Then I should see 3 reviews in the list
        And the first product review in the list should have title "Not bad"
        And the last product review in the list should have title "Great book"

    @ui @todo-api
    Scenario: Sorting product reviews descending by status
        When I sort the product reviews descending by status
        Then I should see 3 reviews in the list
        And the first product review in the list should have title "Great book"
        And the last product review in the list should have title "Not bad"

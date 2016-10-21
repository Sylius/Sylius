@viewing_products
Feature: Viewing product's average rating
    In order to know product reviews summary
    As a Customer
    I want to read product's average rating

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Necronomicon"
        And the store has customer "J.R.R Tolkien" with email "jrr.tolkien@middle-earth.com"
        And this product has accepted reviews rated 5, 3, 4, 4 and 1
        And I am a logged in customer

    @ui
    Scenario: Viewing product's average rating
        When I check this product's details
        Then I should see "3.4" as its average rating

    @ui
    Scenario: Viewing product's average rating calculated only from accepted reviews
        Given this product also has accepted reviews rated 5, 4 and 1
        And this product also has review rated 3 which is not accepted yet
        And this product also has review rated 1 which is rejected
        When I check this product's details
        Then I should see "3.38" as its average rating

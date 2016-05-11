@viewing_products
Feature: Viewing product's attributes
    In order to see product's specification
    As a visitor
    I want to be able to see product's attributes

    Background:
        Given the store operates on a single channel in "France"

    @ui
    Scenario: Viewing a detailed page with product's text attribute
        Given the store has a product "T-shirt banana"
        And this product has text attribute "T-shirt material" with value "Banana skin"
        When I check this product's details
        Then I should see the product attribute "T-shirt material" with value "Banana skin"

    @ui
    Scenario: Viewing a detailed page with product's textarea attribute
        Given the store has a product "T-shirt banana"
        And this product has textarea attribute "T-shirt details" with value "Banana is very good material."
        When I check this product's details
        Then I should see the product attribute "T-shirt details" with value "Banana is very good material."

    @ui
    Scenario: Viewing a detailed page with product's checkbox attribute
        Given the store has a product "T-shirt banana"
        And this product has checkbox attribute "T-shirt with cotton" set to "Yes"
        When I check this product's details
        Then I should see the product attribute "T-shirt with cotton" with value "Yes"

    @ui
    Scenario: Viewing a detailed page with product's date attribute
        Given the store has a product "T-shirt banana"
        And this product has date attribute "T-shirt date of production" with date "12 December 2015"
        When I check this product's details
        Then I should see the product attribute "T-shirt date of production" with value "December 12, 2015 00:00"

    @ui
    Scenario: Viewing a detailed page with product's datetime attribute
        Given the store has a product "T-shirt banana"
        And this product has datetime attribute "T-shirt date of production" with date "12 December 2015 12:34"
        When I check this product's details
        Then I should see the product attribute "T-shirt date of production" with value "December 12, 2015 12:34"

    @ui
    Scenario: Viewing a detailed page with product's percent attribute
        Given the store has a product "T-shirt banana"
        And this product has percent attribute "T-shirt cotton content" with value 50%
        When I check this product's details
        Then I should see the product attribute "T-shirt cotton content" with value "50 %"

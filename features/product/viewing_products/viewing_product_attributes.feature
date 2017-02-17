@viewing_products
Feature: Viewing product's attributes
    In order to see product's specification
    As a visitor
    I want to be able to see product's attributes

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Viewing a detailed page with product's text attribute
        Given the store has a product "T-shirt banana"
        And this product has text attribute "T-shirt material" with value "Banana skin"
        When I check this product's details
        Then I should see the product attribute "T-shirt material" with value "Banana skin"

    @ui
    Scenario: Viewing a detailed page with product's textarea attribute
        Given the store has a product "T-shirt banana"
        And this product has textarea attribute "T-shirt details" with value "Banana is a very good material."
        When I check this product's details
        Then I should see the product attribute "T-shirt details" with value "Banana is a very good material."

    @ui
    Scenario: Viewing a detailed page with product's checkbox attribute
        Given the store has a product "T-shirt banana"
        And this product has checkbox attribute "T-shirt with cotton" set to "Yes"
        When I check this product's details
        Then I should see the product attribute "T-shirt with cotton" with value "Yes"

    @ui
    Scenario: Viewing a detailed page with product's select attribute
        Given the store has a product "T-shirt banana"
        And this product has select attribute "T-shirt material" with values "Banana skin", "cotton"
        When I check this product's details
        Then I should see the product attribute "T-shirt material" with value "Banana skin cotton"

    @ui
    Scenario: Viewing a detailed page with product's date attribute
        Given the store has a product "T-shirt banana"
        And this product has date attribute "T-shirt date of production" with date "12 December 2015"
        When I check this product's details
        Then I should see the product attribute "T-shirt date of production" with value "Dec 12, 2015"

    @ui
    Scenario: Viewing a detailed page with product's datetime attribute
        Given the store has a product "T-shirt banana"
        And this product has datetime attribute "T-shirt date of production" with date "12 December 2015 12:34"
        When I check this product's details
        Then I should see the product attribute "T-shirt date of production" with value "Dec 12, 2015 12:34:00 PM"

    @ui
    Scenario: Viewing a detailed page with product's percent attribute
        Given the store has a product "T-shirt banana"
        And this product has percent attribute "T-shirt cotton content" with value 50%
        When I check this product's details
        Then I should see the product attribute "T-shirt cotton content" with value "50 %"

    @ui
    Scenario: The product attributes are listed by their respective position
        Given the store has a product "T-shirt banana"
        And this product has percent attribute "Wool content" at position 2
        And this product has percent attribute "Polyester content" at position 0
        And this product has percent attribute "Cotton content" at position 1
        When I check this product's details
        Then I should see 3 attributes
        And the first attribute should be "Polyester content"
        And the last attribute should be "Wool content"

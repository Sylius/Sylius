@viewing_products
Feature: Viewing product's attributes
    In order to see product's specification
    As a visitor
    I want to be able to see product's attributes

    Background:
        Given the store operates on a single channel in "United States"

    @ui @api
    Scenario: Viewing a detailed page with product's text attribute
        Given the store has a product "T-Shirt banana"
        And this product has a text attribute "T-Shirt material" with value "Banana skin"
        When I check this product's details
        Then I should see the product attribute "T-Shirt material" with value "Banana skin"

    @ui @api
    Scenario: Viewing a detailed page with product's non-translatable text attribute
        Given the store has a product "T-Shirt banana"
        And this product has non-translatable text attribute "T-Shirt details" with value "Banana is a very good material."
        When I check this product's details
        Then I should see the product attribute "T-Shirt details" with value "Banana is a very good material."

    @ui @api
    Scenario: Viewing a detailed page with product's textarea attribute
        Given the store has a product "T-Shirt banana"
        And this product has a textarea attribute "T-Shirt details" with value "Banana is a very good material."
        When I check this product's details
        Then I should see the product attribute "T-Shirt details" with value "Banana is a very good material."

    @ui @api
    Scenario: Viewing a detailed page with product's non-translatable textarea attribute
        Given the store has a product "T-Shirt banana"
        And this product has non-translatable textarea attribute "T-Shirt details" with value "Banana is a very good material."
        When I check this product's details
        Then I should see the product attribute "T-Shirt details" with value "Banana is a very good material."

    @ui @api
    Scenario: Viewing a detailed page with product's checkbox attribute
        Given the store has a product "T-Shirt banana"
        And this product has a "checkbox" attribute "T-Shirt with cotton" set to "Yes"
        When I check this product's details
        Then I should see the product attribute "T-Shirt with cotton" with positive value

    @ui @api
    Scenario: Viewing a detailed page with product's checkbox non-translatable attribute
        Given the store has a product "T-Shirt banana"
        And this product has non-translatable "checkbox" attribute "T-Shirt with cotton" set to "Yes"
        When I check this product's details
        Then I should see the product attribute "T-Shirt with cotton" with positive value

    @ui @api
    Scenario: Viewing a detailed page with product's date attribute
        Given the store has a product "T-Shirt banana"
        And this product has a date attribute "T-Shirt date of production" with date "12 December 2015"
        When I check this product's details
        Then I should see the product attribute "T-Shirt date of production" with date "Dec 12, 2015"

    @ui @api
    Scenario: Viewing a detailed page with product's date non-translatable attribute
        Given the store has a product "T-Shirt banana"
        And this product has non-translatable date attribute "T-Shirt date of production" with date "12 December 2015"
        When I check this product's details
        Then I should see the product attribute "T-Shirt date of production" with date "Dec 12, 2015"

    @ui @api
    Scenario: Viewing a detailed page with product's datetime attribute
        Given the store has a product "T-Shirt banana"
        And this product has non-translatable datetime attribute "T-Shirt date of production" with date "12 December 2015 12:34"
        When I check this product's details
        Then I should see the product attribute "T-Shirt date of production" with date "Dec 12, 2015 12:34:00 PM"

    @ui @api
    Scenario: Viewing a detailed page with product's datetime non-translatable attribute
        Given the store has a product "T-Shirt banana"
        And this product has non-translatable datetime attribute "T-Shirt date of production" with date "12 December 2015 12:34"
        When I check this product's details
        Then I should see the product attribute "T-Shirt date of production" with date "Dec 12, 2015 12:34:00 PM"

    @ui @api
    Scenario: Viewing a detailed page with product's percent attribute
        Given the store has a product "T-Shirt banana"
        And this product has a percent attribute "T-Shirt cotton content" with value 50%
        When I check this product's details
        Then I should see the product attribute "T-Shirt cotton content" with value 50%

    @ui @api
    Scenario: Viewing a detailed page with product's percent non-translatable attribute
        Given the store has a product "T-Shirt banana"
        And this product has non-translatable percent attribute "T-Shirt cotton content" with value 50%
        When I check this product's details
        Then I should see the product attribute "T-Shirt cotton content" with value 50%

    @ui @api
    Scenario: The product attributes are listed by their respective position
        Given the store has a product "T-Shirt banana"
        And this product has percent attribute "Wool content" at position 2
        And this product has percent attribute "Polyester content" at position 0
        And this product has percent attribute "Cotton content" at position 1
        When I check this product's details
        Then I should see 3 attributes
        And the first attribute should be "Polyester content"
        And the last attribute should be "Wool content"

@viewing_products
Feature: Viewing product's select attributes
    In order to see product's specification
    As a visitor
    I want to be able to see product's select attributes

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-shirt banana"
        And there is an administrator "sylius@example.com" identified by "sylius"

    @ui
    Scenario: Viewing a detailed page with product's select attribute
        Given this product has select attribute "T-shirt material" with values "Banana skin", "Cotton"
        When I check this product's details
        Then I should see the product attribute "T-shirt material" with value "Banana skin, Cotton"

    @ui
    Scenario: Viewing a detailed page with product's select attribute after changing a value
        Given this product has select attribute "T-shirt material" with values "Banana skin", "Cotton"
        When the administrator changes this product attribute's value "Cotton" to "Orange skin"
        And I check this product's details
        Then I should see the product attribute "T-shirt material" with value "Banana skin, Orange skin"

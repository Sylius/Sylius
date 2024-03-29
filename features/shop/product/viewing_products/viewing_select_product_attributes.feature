@viewing_products
Feature: Viewing product's select attributes
    In order to see product's specification
    As a visitor
    I want to be able to see product's select attributes

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt banana"
        And the store has a select product attribute "T-Shirt material" with values "Banana skin" and "Cotton"
        And there is an administrator "sylius@example.com" identified by "sylius"

    @ui @api
    Scenario: Viewing a detailed page with product's select attribute
        Given this product has select attribute "T-Shirt material" with values "Banana skin" and "Cotton"
        When I check this product's details
        Then I should see the product attribute "T-Shirt material" with value "Banana skin" on the list
        And I should also see the product attribute "T-Shirt material" with value "Cotton" on the list

    @ui @api
    Scenario: Viewing a detailed page with product's select attribute after changing a value
        Given this product has select attribute "T-Shirt material" with values "Banana skin" and "Cotton"
        When this product attribute's value changed from "Cotton" to "Orange skin"
        And I check this product's details
        Then I should see the product attribute "T-Shirt material" with value "Banana skin" on the list
        And I should also see the product attribute "T-Shirt material" with value "Orange skin" on the list

    @ui @javascript @api @no-postgres
    Scenario: Viewing a detailed page with product's select attribute after removing an only value
        Given this product has select attribute "T-Shirt material" with value "Cotton"
        When this product attribute's value "Cotton" has been removed
        And I check this product's details
        Then I should not see the product attribute "T-Shirt material"

    @ui @javascript @api @no-postgres
    Scenario: Viewing a detailed page with product's select attribute after removing one of the value
        Given this product has select attribute "T-Shirt material" with values "Banana skin" and "Cotton"
        When this product attribute's value "Cotton" has been removed
        And I check this product's details
        Then I should see the product attribute "T-Shirt material" with value "Banana skin"

@managing_products
Feature: Browsing products
    In order to manage my shop merchandise
    As an Administrator
    I want to be able to browse products

    Background:
        Given the store has "Dice Brewing" and "Eclipse" products
        And I am logged in as an administrator

    @ui
    Scenario: Browsing defined products
        When I want to browse products
        Then I should see 2 products in the list
        And the product "Dice Brewing" should be in the shop

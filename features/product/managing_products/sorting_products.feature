@managing_products
Feature: Sorting listed products
    In order to change the order by which products are displayed
    As an Administrator
    I want to sort products

    Background:
        Given the store operates on a single channel in "United States"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And the store has a product "Berserk Pug" with code "B_PUG"
        And this product is named "Szałowy Mops" in the "Polish (Poland)" locale
        And the store also has a product "Pug of Love" with code "L_PUG"
        And this product is named "Mops Miłości" in the "Polish (Poland)" locale
        And the store also has a product "Xtreme Pug" with code "X_PUG"
        And this product is named "Ekstremalny Mops" in the "Polish (Poland)" locale
        And I am logged in as an administrator

    @ui
    Scenario: Products are sorted by ascending codes by default
        Given I am browsing products
        Then I should see 3 products in the list
        And I should see a product with code "L_PUG"
        But the first product on the list should have code "B_PUG"

    @ui
    Scenario: Changing the codes sorting order
        Given I am browsing products
        When I switch the way products are sorted by code
        Then I should see 3 products in the list
        And I should see a product with code "B_PUG"
        But the first product on the list should have code "X_PUG"

    @ui
    Scenario: Products can be sorted by their names
        Given I am browsing products
        When I start sorting products by name
        Then I should see 3 products in the list
        And I should see a product with name "Xtreme Pug"
        But the first product on the list should have name "Berserk Pug"

    @ui
    Scenario: Changing the names sorting order
        Given I am browsing products
        And the products are already sorted by name
        When I switch the way products are sorted by name
        Then I should see 3 products in the list
        And I should see a product with name "Berserk Pug"
        But the first product on the list should have name "Xtreme Pug"

    @ui
    Scenario: Products are always sorted in the default locale even if another is active
        Given I change my locale to "Polish (Poland)"
        And I am browsing products
        When I switch the way products are sorted by name
        Then I should still see a product with name "Pug of Love"
        And the first product on the list should have name "Berserk Pug"
        But I should not see any product with name "Szałowy Mops"

    @ui
    Scenario: Changing the names sorting order with active locale different than default
        Given I change my locale to "Polish (Poland)"
        And I am browsing products
        And the products are already sorted by name
        When I switch the way products are sorted by name
        Then I should see 3 products in the list
        And I should see a product with name "Xtreme Pug"
        And the first product on the list should have name "Xtreme Pug"
        But I should not see any product with name "Mops Miłości"

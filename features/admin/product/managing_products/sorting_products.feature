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
        And this product is named "Ekstremalny Mops" in the "Polish" locale
        And I am logged in as an administrator

    @ui @api
    Scenario: Products are sorted by ascending codes by default
        Given I am browsing products
        Then I should see 3 products in the list
        And I should see a product with code "L_PUG"
        But the first product on the list should have code "B_PUG"

    @ui @api
    Scenario: Changing the codes sorting order
        Given I am browsing products
        When I switch the way products are sorted descending by code
        Then I should see 3 products in the list
        And I should see a product with code "B_PUG"
        But the first product on the list should have code "X_PUG"

    @ui @api
    Scenario: Products can be sorted by their names
        Given I am browsing products
        When I start sorting products by name
        Then I should see 3 products in the list
        And I should see a product with name "Xtreme Pug"
        But the first product on the list should have name "Berserk Pug"

    @ui @api
    Scenario: Changing the names sorting order
        Given I am browsing products
        When the products are already sorted ascending by name
        And I switch the way products are sorted descending by name
        Then I should see 3 products in the list
        And I should see a product with name "Berserk Pug"
        But the first product on the list should have name "Xtreme Pug"

    @ui @api
    Scenario: Sort products ascending by name from chosen locale translations
        When I change my locale to "Polish (Poland)"
        And I browse products
        And I sort the products ascending by name
        Then I should see 3 products in the list
        And the first product on the list should have name "Ekstremalny Mops"

    @ui @api
    Scenario: Sort products descending by name from chosen locale translations
        When I change my locale to "Polish (Poland)"
        And I browse products
        And the products are already sorted ascending by name
        And I sort the products descending by name
        Then I should see 3 products in the list
        And the first product on the list should have name "Szałowy Mops"

    @ui @api @no-postgres
    Scenario: Missing translations are sorted as first when sorting by name ascending
        When I change my locale to "Polish"
        And I browse products
        And I sort the products ascending by name
        Then I should see 3 products in the list
        And the first product on the list shouldn't have a name
        And the last product on the list should have name "Ekstremalny Mops"

    @ui @api @no-postgres
    Scenario: Missing translation are sorted as last when sorting by name descending
        When I change my locale to "Polish"
        And I browse products
        And the products are already sorted ascending by name
        And I sort the products descending by name
        Then I should see 3 products in the list
        And the first product on the list should have name "Ekstremalny Mops"
        And the last product on the list shouldn't have a name

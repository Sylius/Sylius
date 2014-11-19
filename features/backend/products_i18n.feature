@products
Feature: Products
    In order to create my offer
    As a store owner
    I want to be able to manage products

    Background:
        Given there is default currency configured
        And I am logged in as administrator
        And there are following options:
            | name          | presentation | values           |
            | T-Shirt color | Color        | Red, Blue, Green |
            | T-Shirt size  | Size         | S, M, L          |
        And there are following attributes:
            | name               | presentation      | type     | choices   |
            | T-Shirt fabric     | T-Shirt           | text     |           |
            | T-Shirt fare trade | Faretrade product | checkbox |           |
            | Color              | color             | choice   | red, blue |
            | Size               | size              | number   |           |
        And the following products exist:
            | name          | price | options                     | attributes             |
            | Super T-Shirt | 19.99 | T-Shirt size, T-Shirt color | T-Shirt fabric: Wool   |
            | Black T-Shirt | 19.99 | T-Shirt size                | T-Shirt fabric: Cotton |
            | Mug           | 5.99  |                             |                        |
            | Sticker       | 10.00 |                             |                        |
        And product "Super T-Shirt" is available in all variations
        And there are following tax categories:
            | name        |
            | Clothing    |
            | Electronics |
            | Print       |
        And there are following taxonomies defined:
            | name     |
            | Category |
            | Special  |
        And taxonomy "Category" has following taxons:
            | Clothing > T-Shirts         |
            | Clothing > Premium T-Shirts |
        And taxonomy "Special" has following taxons:
            | Featured |
            | New      |
        And there are following locales configured:
            | code  | enabled |
            | de_DE | yes     |
            | en_US | no      |
            | es    | yes     |
        And the following product translations exist:
            | product       | translation    | code |
            | Super T-Shirt | Super Camiseta | es   |

#    TODO enforce to have at least default locale translation?
    Scenario: Submitting form without specifying the name
        Given I am on the product creation page
        And I fill in the following:
            | Name  | Book about Everything |
            | Price | 29.99                 |
        When I press "Create"
        Then "Product has been successfully created." should appear on the page

    @javascript
    Scenario: Creating a product in specific locale
        Given I am on the product creation page
        And go to "Es" tab
        And I fill in the following:
            | Price                                      | 29.99                    |
#            Using 'name' a 'Element is not currently visible and so may not be interacted with' is thrown
#            maybe because it finds the first tab's 'name' in a language that's hidden
#            will leave it like this for now as backend is going to change
            | sylius_product_translations_es_name        | Soy i18n                 |
            | sylius_product_translations_es_description | Conmemorando el dia i18n |
        When I press "Create"
        Then "Product has been successfully created." should appear on the page
        And "Es" translation for product "Soy i18n" should exist


@i18n
Feature: Browse products, categories, attributes and options in preferred language
    In order to be able to understand what is being sold in the shop
    As a visitor
    I want to be able to browse products in my preferred language

    Background:
        Given there is default currency configured
        And there are following taxonomies defined:
            | name     |
            | Category |
        And taxonomy "Category" has following taxons:
            | Clothing > T-Shirts     |
            | Clothing > PHP T-Shirts |
        And there are following options:
            | name          | presentation | values           |
            | T-Shirt color | Color        | Red, Blue, Green |
            | T-Shirt size  | Size         | S, M, L          |
        And there are following attributes:
            | name               | presentation      | type     | choices   |
            | T-Shirt fabric     | Fabric            | text     |           |
        And the following products exist:
            | name          | price | options                     | attributes             | taxons       |
            | Super T-Shirt | 19.99 | T-Shirt size, T-Shirt color | T-Shirt fabric: Wool   | T-Shirts     |
        And product "Super T-Shirt" is available in all variations
        And there are following locales configured:
            | code  | enabled |
            | en    | yes     |
            | es    | yes     |
        And the following product translations exist:
            | product       | name           | locale |
            | Super T-Shirt | Camiseta Super | es     |
        And the following taxonomy translations exist
            | taxonomy | name      | locale |
            | Category | Categoria | es     |
        And the following taxon translations exist
            | taxon    | name      | locale |
            | Clothing | Ropa      | es     |
            | T-Shirts | Camisetas | es     |
        And the following attribute translations exist
            | attribute      | presentation | locale |
            | T-Shirt fabric | Material     | es     |
        And the following option translations exist
            | option       | presentation | locale |
            | T-Shirt size | Talla        | es     |

    Scenario: Seeing translated product, taxonomy and taxon name
        Given I am on the store homepage
        When I change the locale to "Spanish"
        Then I should see "Camiseta Super"
        And I should see "Categoria"
        And I should see "Camisetas"

    Scenario: Seeing translated product options and attributes
        Given I am on the store homepage
        When I change the locale to "Spanish"
        And I follow "Camiseta Super"
        Then I should see "Material"
        And I should see "Talla"

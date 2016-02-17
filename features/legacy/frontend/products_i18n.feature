@legacy_i18n
Feature: Browse products, categories, attributes and options in preferred language
    In order to be able to understand what is being sold in the shop
    As a visitor
    I want to be able to browse products in my preferred language

    Background:
        Given store has default configuration
        And there are following taxonomies defined:
            | code | name     |
            | RTX1 | Category |
        And taxonomy "Category" has following taxons:
            | Clothing[TX1] > T-Shirts[TX2]     |
            | Clothing[TX1] > PHP T-Shirts[TX3] |
        And there are following options:
            | code | name          | presentation | values                          |
            | O1   | T-Shirt color | Color        | Red[OV1], Blue[OV2], Green[OV3] |
            | O2   | T-Shirt size  | Size         | S[OV4], M[OV5], L[OV6]          |
        And there are following attributes:
            | name           | presentation | type | choices |
            | T-Shirt fabric | Fabric       | text |         |
        And the following products exist:
            | name          | price | options                     | attributes           | taxons   |
            | Super T-Shirt | 19.99 | T-Shirt size, T-Shirt color | T-Shirt fabric: Wool | T-Shirts |
        And product "Super T-Shirt" is available in all variations
        And there are following locales configured and assigned to the default channel:
            | code  |
            | en_US |
            | es_ES |
        And the following product translations exist:
            | product       | name           | locale |
            | Super T-Shirt | Camiseta Super | es_ES  |
        And the following taxonomy translations exist:
            | taxonomy | name      | locale |
            | Category | Categoria | es_ES  |
        And the following taxon translations exist:
            | taxon    | name      | locale |
            | Clothing | Ropa      | es_ES  |
            | T-Shirts | Camisetas | es_ES  |
        And the following attribute translations exist:
            | attribute      | name     | locale |
            | T-Shirt fabric | Material | es_ES  |
        And the following option translations exist:
            | option       | presentation | locale |
            | T-Shirt size | Talla        | es_ES  |
        And all products are assigned to the default channel

    Scenario: Seeing translated product name, options and attributes
        Given I am on the store homepage
        When I change the locale to "Spanish (Spain)"
        And I follow "Camiseta Super"
        Then I should see "Material"
        And I should see "Talla"

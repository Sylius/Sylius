@products
Feature: Product association
  In order connect products together in many contexts
  As a store owner
  I want to be able associate product with another ones

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


    Scenario: Create association type
       Given I want to create new association type
       When I create "Cross sell" association type
       Then I should be able to add "Cross sell" associations to every product

    Scenario Outline: Associate product with others products
      Given there are following association types:
        | name       |
        | Cross sell |
        | Upsell     |
        | Different  |
      And I want to assign new association for "<Product name>" product
      When I select "<Associated product name>" product as "<Association type>" association
      Then I should see that "<Product name>" is connected with "<Associated product name>" by "<Association type>" association

      Examples:
        | Product name  | Associated product name | Association type |
        | Black T-Shirt | Super T-Shirt           |      Cross sell  |
        | Black T-Shirt | Mug                     |       Different  |

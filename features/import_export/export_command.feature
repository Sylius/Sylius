@importexport
Feature: Export command
  In order to export data from my store
  As a store owner
  I want to be able to run jobs based on defined export profiles

  Background:
    Given there are following export profiles configured:
      | name               | description | code        | reader   | reader_configuration                  | writer     | writer_configuration                              |
      | UsersExportProfile | Lorem ipsum | user_export | user_orm | batch_size:10,date_format:Y-m-d H:i:s | csv_writer | Delimiter:;,Enclosure:",File path:\tmp\output.csv |
    And there is default currency configured
    And there is default channel configured
    And there are following users:
      | email          | enabled  | created_at          |
      | beth@foo.com   | yes      | 2010-01-02 12:00:00 |
      | martha@foo.com | yes      | 2010-01-02 13:00:00 |
      | rick@foo.com   | yes      | 2010-01-03 12:00:00 |
    And I am logged in as administrator

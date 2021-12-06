Legacy API
==========

We have decided to rebuild our APIs and unify them with API Platform.
Previously we had 2 separate APIs for shop (`SyliusShopAPi Plugin) <https://github.com/Sylius/ShopApiPlugin>`_, and for admin (`SyliusAdminApiBundle <https://github.com/Sylius/SyliusAdminApiBundle>`_).
Both of them are using the `FOSRestBundle <https://github.com/FriendsOfSymfony/FOSRestBundle>`_, and make operation using commands and events.
This approach is easy to understand and implement, but when we need to customize something we need to overwrite many files (command, event, command handler, event listener etc).
The second reason to create a new Sylius API from scratch is that the API Platform is a modern framework for API and it replaces FOSRestBundle.
We will fix security issues in our legacy APIs but all new features will be developed only in the new API.


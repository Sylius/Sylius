# Sylius API - Authorization

As introduced in [this PR](https://github.com/Sylius/Sylius/pull/11174), you need to authorize with JWT token to be able
to use new Sylius API.

1. Generate SSH keys for JWT

   ```bash
   openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
   openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
   ```

   > Paths for the keys are configured in `.env` files

2. Request for JWT token by the authentication request

   ```bash
   curl -X POST http://127.0.0.1:8000/new-api/authentication-token -H "Content-Type: application/json" -H "Accept: application/json" -d '{"email": "api@example.com", "password": "sylius-api"}'
   ```
   
   > Email "api@example.com" and password "sylius-api" are default credentials for API administrator provided in the default
     [fixtures suite](https://github.com/Sylius/Sylius/blob/0e4ed2e34e7f255aacef02a43cc2e7bf006d03fd/src/Sylius/Bundle/CoreBundle/Resources/config/app/fixtures/shop_configuration.yaml#L158)

   In the response you will get a token that need to be passed in each request header.
   
   ```json
   {"token": "VERY_SECURE_TOKEN"}
   ```
   
3. If you're using Api Platform Swagger docs (available on `/new-api/docs/` URL), you can set the authentication token
for each request.

    i. Go the the Swagger docs page. Notice the **Authorize** button and unlocked padlock near the available URLs:
    
    ![not-authorized](https://raw.githubusercontent.com/Zales0123/Sylius/api-authorization-docs/src/Sylius/Bundle/ApiBundle/docs/images/api-platform-not-authorized.png)
    
    ii. Click the **Authorize** button and put the authentication token (remember about the `Bearer` prefix):
    
    ![not-authorized](https://raw.githubusercontent.com/Zales0123/Sylius/api-authorization-docs/src/Sylius/Bundle/ApiBundle/docs/images/api-platform-authorization.png)
    
    iii. After clicking **Authorize**, you should see locked padlock near URLs and the proper header should be added to
    each API call 
    
    ![not-authorized](https://raw.githubusercontent.com/Zales0123/Sylius/api-authorization-docs/src/Sylius/Bundle/ApiBundle/docs/images/api-platform-authorized.png)

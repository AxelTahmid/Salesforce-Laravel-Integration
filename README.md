# Laravel Salesforce OAuth Rest Api

`Axel Tahmid` `www.tahmid.org` `axel.tahmid@gmail.com` `bjsmasth` `laravel salesforce OAuth`

# Getting Started

Setting up a Connected App

1. Log into to your Salesforce org
2. Click on Setup in the upper right-hand menu
3. Under Build click `Create > Apps `
4. Scroll to the bottom and click `New` under Connected Apps.
5. Enter the following details for the remote application:
    - Connected App Name
    - API Name
    - Contact Email
    - Enable OAuth Settings under the API dropdown
    - Callback URL
    - Select access scope (If you need a refresh token, specify it here)
6. Click Save

After saving, you will now be given a Consumer Key and Consumer Secret. Update your config file with values for `consumerKey` and `consumerSecret`

# Setup

Authentication

```bash
    .env Variables

        SF_AUTH_METHOD= "password"
        SF_CLIENT_ID="CONSUMERKEY"
        SF_CLIENT_SECRET="CONSUMERSECRET"
        SF_LOGIN_URL="SALESFORCE LOGIN URL"
        SF_USERNAME="SALESFORCE_USERNAME"
        SF_PASSWORD="SALESFORCE_PASSWORD AND SECURITY_TOKEN"
```

# About
A Laravel-based API service for drug information search and user-specific drug tracking. \
The service is integrated with the National Library of Medicine's RxNorm APIs for drug data.

# Table of Contents
- [Installation](#installation)
    - [Common Task](#common-task)
    - [Normal Installation](#normal-installation)
    - [Docker Installation](#docker-installation)
- [Endpoints](#endpoints)
- [Unit tests](#unit-tests)
- [Rate Limit](#rate-limit)
- [Cache](#cache)
- [Postman Collection](#postman-collection)
- [SQL Files](#sql-files)


# Installation:

## Common Task
After pulling from the repository
- Copy `.env.example` and paste as `.env`. Change anything such as port number or db configuration if you feel necessary
- Redis and Database has 2 types of values for local and docker configuration, set according to your setup.

## Normal Installation
1. Run command: `composer install`
2. _(If needed)_, give storage permission: `chmod -R 777 storage`
3. _(If needed)_, give permission to: `chmod -R 777 bootstrap/cache/`
4. Migrate database: `php artisan migrate`
5. Add JWT secret key: `php artisan jwt:secret`
6. Update `JWT_TTL` & `JWT_REFRESH_TTL` value in your `.env` file _(if you feel necessary)_

## Docker Installation
1. Copy `docker-compose.yml.example` and paste as `docker-compose.yml.`. Change anything such as port number or db configuration if you feel necessary
2. Run command: `docker compose up -d` \
   Or,
    - Run command `docker compose build --no-cache` for a fresh build
    - Then, run command: `docker compose up -d`
3. Go to the bash script of the php related container:
    - Run commnad: `docker compose exec php bash`
    - Change directory: `cd ../project/`
4. **Inside the container:**
    - To install composer packages, run command: `composer install`
    - _(If needed)_, give storage permission: `chmod -R 777 storage`
    - give permission to: `chmod -R 777 bootstrap/cache/`
    - Add JWT secret key: `php artisan jwt:secret`
    - Migrate database: `php artisan migrate`


# Endpoints
1. **User Authentication Endpoints**
    - **Register User**:
        - Description: Allows new users to register.
        - Payload: **`name`**, **`email`**, **`password`**.
    - **Login User**:
        - Description: Allows users to log in.
        - Payload: **`email`**, **`password`**.
2. **Public Search Endpoint (Unauthenticated)**
    - **Description**: Search for drugs using the RxNorm “getDrugs” endpoint (from https://lhncbc.nlm.nih.gov/RxNav/APIs/RxNormAPIs.html).
    - **Parameters**: **`drug_name`** (string)
    - **Functionality**:
        - Use the “getDrugs” endpoint from the National Library of Medicine for tty = “SBD”.
        - Fetch the “name” of the top 5 results.
        - Additionally, use *getRxcuiHistoryStatus* API from National Library of Medicine to fetch:
            - All `baseName` under `ingredientAndStrength`.
            - Different `doseFormGroupName` from `doseFormGroupConcept`.
        - Return fields: **rxcui (ID)**, **Drug name** (string), **Ingredient base names** (array), **Dosage form** (array).
3. **Private User Medication Endpoints** (Ensure that all endpoints below are authenticated)
    - **Endpoints**:
        - **Add Drug**:
            - Description: Add a new drug to the user's medication list.
            - Payload: `rxcui` (string)
            - Validation: Ensure `rxcui` is valid (using National Library of Medicine API).
        - **Delete Drug**:
            - Description: Delete a drug from the user's medication list.
            - Validation: Ensure `rxcui` is valid and exists in the user’s list.
        - **Get User Drugs**:
            - Description: Retrieve all drugs from the user's medication list.
            - Returns: Rx ID, Drug name, baseNames (ingredientAndStrength), doseFormGroupName (doseFormGroupConcept).


# Unit tests:

- Run command: `php artisan config:cache --env=testing` (_either in docker container or in your local based on your setup_)
- then run: `php artisan migrate --env=testing`
- The above 2 commands will separate your testing DB from main DB
- Run each test file one by one in this sequel**, (_because we need `ACCESS_TOKEN` for some test file_):
- Run tests file in this sequel:
    - DrugSearchTest, RegistrationTest, LoginTest, AddUserDrugTest, GetUserDrugsTest, DeleteDrugTest
    - Because, otherwise for authenticated route, you won't get the access token.
- To run a single test file, run with filepath, like, : `php artisan test tests/Unit/RegistrationTest.php`
- After your test run is complete reset the env
```shell
    php artisan config:clear
    php artisan cache:clear
```
Otherwise, postman API call will access the testing database.

# Rate Limit
- Rate limiter values are taken from env value, you can update as you need

# Cache

- Cache is applied in places:
  - where the RxNorm APIs are called and GET user's drug API
  - **In Login unit test:** to store the access token, in order to use that token by other unit test class where the authentication token is needed.

# Error Log
- Error logs are generated in daily log file inside `storage/log`, so that it can be separated for each day
- The file names are like this, `laravel-2023-12-10.log`


# Postman Collection
- Documentation link: https://documenter.getpostman.com/view/1449184/2sB3QQHmvs \
You can download/run collection from this link in your postman

# SQL Files
- I provided my sql db file. You can find it inside the `sql_files` folder

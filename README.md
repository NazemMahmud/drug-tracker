## About
A Laravel-based API service for drug information search and user-specific medication tracking.
The service is integrated with the National Library of Medicine's RxNorm APIs for drug data.



# Unit tests:

- Run command: `php artisan config:cache --env=testing` (_either in docker container or in your local based on your setup_)
- then run: `php artisan migrate --env=testing`
- The above 2 commands will separate your testing DB from main DB
- To run a single test file, run with filepath, like, : `php artisan test tests/Unit/RegistrationTest.php`

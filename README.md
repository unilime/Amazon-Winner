## Requirements
* Docker (Docker Compose)
* GIT

## Local development installation
1. Clone project to  the  local machine
`git clone [project_url] [project_folder]`
2. Open  project folder
`cd [projerct_folder]`
3. Copy .env from the 'docs/dev' to the project root `cp docs/dev/.env .`
4. Run `docker-compose up -d` to build and start containers
5. SSH to the PHP container `docker-compose exec php-cli-app bash`
6. Install all project requirements `composer install`
7. Setup reports folder `mkdir storage/reports`
8. Run script `php bin/amazon_recommendation_scraper.php -f storage/requests/test_request.txt` (`-f` attribute is required)
9. Check the generated report in `storage/reports`

## Requests
Each line mean one set of keywords, for example storage/requests/test_request.txt

## Usage
* `docker-compose up -d` - build and run containers
* `docker-compose stop` - stop containers
* `docker-compose exec php-cli-app bash` - SSH to the PHP container

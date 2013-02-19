# AmsterdamPHP - Meetup.com Raffler

This application enables us to raffle off stuff to our meetup attendees. It uses the Meetup.com API to get all check ins, and then uses the Random.org API to randomly select winners.

## Installation

1. Get code
2. Give permissions

    ```
    sudo chmod -Rf +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" cache logs
    sudo chmod -Rf +a "<apache user> allow delete,write,append,file_inherit,directory_inherit" cache logs
    ```

3. Compile stylesheets

    ```
    compass compile
    ```

4. Create config/parameters.yml

    ```
    meetup_group:   amsterdamphp
    meetup_api_key: YOUR_MEETUP_API_KEY
    ```

## How to use it
1. Open the app index page to be presented with a list of meetups.
2. Click on a meetup.
3. Press [space] or [page down] to start raffles.

The page down key allows us to use most presentation remotes as well.
# AmsterdamPHP - Meetup.com Raffler

This application enables us to raffle off stuff to our meetup attendees. It uses the Meetup.com API to get all check ins, and then uses the Random.org API to randomly select winners.

## Installation

1. Get code
2. Give permissions

    sudo chmod -Rf +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" cache logs
    sudo chmod -Rf +a "<apache user> allow delete,write,append,file_inherit,directory_inherit" cache logs

3. Run compile stylesheets

    compass compile

4. Create config/parameters.yml

    meetup_group:   amsterdamphp
    meetup_api_key: YOUR_MEETUP_API_KEY
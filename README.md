# AmsterdamPHP - Meetup.com Raffler

This application enables us to raffle off stuff to our meetup attendees. It uses the Meetup.com API to get all check ins, and then randomly selects winners.

## Installation

### Install into a virtual machine. (Linux and OSX users only)

Ensure that [VirtualBox](https://www.virtualbox.org), [Vagrant](http://www.vagrantup.com), and [Ansible](http://www.ansible.com) are installed.

1. `git clone git@github.com:AmsterdamPHP/meetup-raffle-machine.git --recursive`
2. run `vagrant up`
3. create `config/parameters.yml` (get your API key from [meetup.com](https://secure.meetup.com/meetup_api/key/))

    ```
    parameters:
      meetup_group:   amsterdamphp
      meetup_api_key: YOUR_MEETUP_API_KEY
      secret:         SomeRandomSecretToSeedSymfony
      redis_dsn:      redis://locahost
    ```
4. Add the following to your hosts file: `10.10.10.10 raffler.local`

All done! Now you can access the application at [http://raffler.local/](http://raffler.local/).

### Install directly onto your host machine.

1. Get code
2. Give permissions

    ```
    sudo chmod -Rf +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" cache logs
    sudo chmod -Rf +a "<apache user> allow delete,write,append,file_inherit,directory_inherit" cache logs
    ```

3. Install Dependencies

    ```
    composer install
    npm install
    ```

4. Compile assets

    ```
    ./node_modules/.bin/gulp
    ```

5. Create config/parameters.yml

    ```
    parameters:
      meetup_group:   amsterdamphp
      meetup_api_key: YOUR_MEETUP_API_KEY
      secret:         SomeRandomSecretToSeedSymfony
      redis_dsn:      redis://locahost
    ```

## How to use it

1. Open the app index page to be presented with a list of meetups.
2. Click on a meetup.
3. Press [space] or [page down] to start raffles.

The page down key allows us to use most presentation remotes as well.

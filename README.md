# AmsterdamPHP - Meetup.com Raffler

This application enables us to raffle off stuff to our meetup attendees. It uses the Meetup.com API to get all check ins, and then randomly selects winners.

## Installation

### Install into a virtual machine. (Linux and OSX users only)

Ensure that [VirtualBox](https://www.virtualbox.org), [Vagrant](http://www.vagrantup.com), and [Ansible](http://www.ansible.com) are installed.

1. `git clone git@github.com:AmsterdamPHP/meetup-raffle-machine.git --recursive`
1. run `vagrant up`
1. create `config/parameters.yml` (get your API key from [meetup.com](https://secure.meetup.com/meetup_api/key/))

    ```
    parameters:
      meetup_group:   amsterdamphp
      meetup_api_key: YOUR_MEETUP_API_KEY
      secret:         SomeRandomSecretToSeedSymfony
      redis_dsn:      redis://localhost
    ```
1. Add the following to your hosts file: `10.10.10.10 raffler.local`

All done! Now you can access the application at [http://raffler.local/](http://raffler.local/).

### Install directly onto your host machine.

1. Get code
1. Give permissions

    ```
    sudo chmod -Rf +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" cache logs
    sudo chmod -Rf +a "<apache user> allow delete,write,append,file_inherit,directory_inherit" cache logs
    ```

1. Install Dependencies

    ```
    make install
    ```
1. Create config/parameters.yml

    ```
    parameters:
      meetup_group:   amsterdamphp
      meetup_api_key: YOUR_MEETUP_API_KEY
      secret:         SomeRandomSecretToSeedSymfony
      redis_dsn:      redis://localhost
    ```

## How to use it

1. Open the app index page to be presented with a list of meetups.
1. Click on a meetup.
1. Press [space] or [page down] to start raffles.

The page down key allows us to use most presentation remotes as well.

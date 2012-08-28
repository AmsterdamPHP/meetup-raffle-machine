# AmsterdamPHP - Meetup.com Raffler

This project is a simple implementation of a raffle machine. It integrates into the meetup.com API getting current RSVP'd members and randomly selects one winner from the list. It uses random.org to select the winner to provide the best random chances for everyone.

## Installation

1. Get Code
2. Give Permissions

    sudo chmod -Rf +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" cache logs
    sudo chmod -Rf +a "<apache user> allow delete,write,append,file_inherit,directory_inherit" cache logs
		
3. To do..
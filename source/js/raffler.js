/**
 * Main object.
 */
var Raffler = {
    /**
     * The delay between highlighting each checkin.
     */
    highlightDelay: 0,

    /**
     * The current hightlight cycle.
     */
    currentCycle: 0,

    /**
     * Array of truely random numbers from random.org. This is populated
     * on page load. We select the winners from this array.
     */
    winners: [],

    /**
     * Current state we are in. One of "start", "raffling", "winner"
     */
    state: 'start',

    flushConst: 'flush',
    raffleConst: 'raffle',

    /**
     * Initialize
     */
    init: function(winners) {
        Raffler.winners = winners;
        $(document).on('keydown', Raffler.onKeyDown);
    },

    /**
     * On key down handler.
     */
    onKeyDown: function(e) {

        var key = e.keyCode;

        // We only handle the space (32) and page down (34) keys. Page down is enabled because
        // presentation remotes emit page down on the "next" button
        if (acceptedKeys[key] === undefined) {
            return;
        }

        if (acceptedKeys[key] == Raffler.flushConst) {
            Raffler.flushAbsent();
            Raffler.resetRaffler();
            return;
        }

        // If we are in start state, start the raffler
        if (Raffler.state == 'start') {
            Raffler.raffle();
        }

        // If we are raffling, do nothing.
        if (Raffler.state == 'raffling') {
            return;
        }

        // If we are showing the winner, reset the state
        if (Raffler.state == 'winner') {
            Raffler.resetRaffler();
        }
    },

    /**
     * Raffle.
     */
    raffle: function () {

        if (Raffler.winners.length <= 0 ) {
            alert("We have gone over the initial draft, please refresh page.");
            window.history.go(0);
            return;
        }

        Raffler.hideCheckinHint();

        Raffler.state = 'raffling';
        Raffler.highlightRandomCheckin();
    },

    hideCheckinHint: function() {
        $('.tap-to-checkin').hide();
        $('.not_checked_in').unbind('click');
    },

    /**
     * Present a winner.
     */
    showWinner: function() {
        // Change state
        Raffler.state = 'winner';

        // Hide all checkins
        $('.checkin').addClass('loser', 1000);


        // Show winner
        var winner = $('.checkin').eq(Raffler.winners.pop());
        winner.switchClass('loser', 'winner', 200);
    },

    /**
     * Reset raffler
     */
    resetRaffler: function() {
        // Reset cycles and delay
        Raffler.currentCycle = 0;
        Raffler.highlightDelay = 0;

        // Reset styles
        $('.checkin').removeClass('loser');
        $('.checkin').removeClass('winner');

        // Reset state
        Raffler.state = 'start';
    },

    /**
     * Highlight random checkin.
     */
    highlightRandomCheckin: function() {
        // Abort if we have reached 50 cycles
        if (45 <= Raffler.currentCycle) {
            Raffler.showWinner();
            return;
        }

        // Increase the current highlight cycle
        Raffler.currentCycle++;

        // Adjust the highlight delay
        Raffler.highlightDelay = Math.pow(1.14, Raffler.currentCycle);

        // Get random person to highlight
        checkin = Raffler.getRandomCheckin();

        // Highlight, delay, unhighlight
        checkin.addClass('selected', Raffler.highlightDelay, Raffler.unhighlightCurrentCheckin);
    },

    /**
     * Unhighlight current checkin
     */
    unhighlightCurrentCheckin: function() {
        // Unhighlight this checkin and recurse back to highlighting another
        // random checkin
        $(this).removeClass('selected', Raffler.highlightDelay, Raffler.highlightRandomCheckin);
    },

    /**
     * Get random checkin.
     */
    getRandomCheckin: function() {
        var random = Math.floor(Math.random() * $('.checkin').size());
        return($('.checkin').eq(random));
    },

    flushAbsent: function() {
        $('.not_checked_in').remove();
        $('.checked_in').removeClass('checked_in');
        Raffler.hideCheckinHint();
    }
};

var acceptedKeys = {
    27:  Raffler.flushConst ,
    32 : Raffler.raffleConst,
    34 : Raffler.raffleConst
};

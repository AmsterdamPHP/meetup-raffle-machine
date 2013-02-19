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
     * Whether we are cycling.
     */
    cycling: false,

    /**
     * Initialize
     */
    init: function() {
        $(document).on('keydown', Raffler.onKeyDown);
    },

    /**
     * On key down handler.
     */
    onKeyDown: function(e) {
        // Do nothing if the raffler is running
        if (Raffler.cycling) {
            return;
        }

        // Raffle on space(32) and page down (34). Page down is enabled because
        // presentation remotes emit page down on the "next" button
        if (e.keyCode == 32 || e.keyCode == 34) {
            Raffler.raffle();
        }
    },

    /**
     * Raffle.
     */
    raffle: function () {
        // Reset state
        Raffler.currentCycle = 0;
        Raffler.highlightDelay = 0;

        // Start cycling
        Raffler.cycling = true;
        Raffler.highlightRandomCheckin();
    },

    /**
     * Highlight random checkin.
     */
    highlightRandomCheckin: function() {
        // Abort if we have reached 50 cycles
        if (50 <= Raffler.currentCycle) {
            Raffler.cycling = false;
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
    }
};
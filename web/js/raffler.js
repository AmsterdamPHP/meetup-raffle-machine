/**
 * Main object.
 */
var Raffler = {
    /**
     * Initialize
     */
    init: function() {
        $(document).on('keydown', function(e) {
            if (e.keyCode == 32) {
                Raffler.raffle();
            }
        });
    },

    /**
     * Raffle.
     */
    raffle: function () {
        window.setInterval(Raffler.highlightRandomCheckin, 250);
    },

    /**
     * Get a random checkin.
     */
    highlightRandomCheckin: function() {
        var min = 0;
        var max = $('.checkin').size() - 1;
        var random = Math.floor(Math.random() * (max - min + 1)) + min;
        $('.checkin').removeClass('selected');
        return $('.checkin').eq(random).addClass('selected');
    }
};
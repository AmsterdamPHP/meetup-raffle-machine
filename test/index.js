require('mocha-generators').install();
var path = require('path');
var Nightmare = require('nightmare');
var expect = require('chai').expect;

describe('AmsterdamPHP Raffler', function () {
    this.timeout(30000); // Set timeout to 15 seconds, instead of the original 2 seconds
    var url = 'http://localhost:8080',
        nightmare;

    beforeEach(function *() {
        nightmare = Nightmare({
            show: true,
            webPreferences: {
                preload: path.join(__dirname, 'preload', 'confirm-checkin.js')
            }
        });

        nightmare.on('console', function (type, str) {
            console.log(type, str);
        });
    });

    afterEach(function *() {
        yield nightmare.end();
    });

    describe('Event List', function () {
        it('should show a list of current/past events', function *() {
            var result = yield nightmare
                .goto(url)
                .evaluate(function () {
                    var list = document.querySelectorAll('ul.meetups > li');

                    return {
                        length: list.length,
                        last_date: list.item(0).getAttribute('data-date')
                    };
                });

            var one_month_ahead = Date.now() + (31 * 24 * 60 * 60 * 1000);

            expect(result.length).to.be.above(1);
            expect(result.last_date).to.be.below(one_month_ahead);
        });

        describe('Event Page', function () {
            var event_page = '';

            it('should have a checkin link', function *() {
                var has_checkins_div = yield nightmare
                    .goto(url)
                    .click('ul.meetups > li > a')
                    .exists('div.checkins');

                expect(has_checkins_div).to.be.true;

                var has_checkin_link = yield nightmare
                    .exists('p.checkin-link > a');

                expect(has_checkin_link).to.be.true;

                event_page = yield nightmare
                    .url()
            });

            describe('Check In Page', function () {

                var amount_checkins = 0;

                it('should be reachable', function *() {
                    amount_checkins = yield nightmare
                        .goto(event_page)
                        .evaluate(function () {
                            return document.querySelectorAll('div.checkins > div.checkin').length;
                        });

                    var checkin_page = yield nightmare
                        .click('p.checkin-link > a')
                        .exists('p.tap-to-checkin');

                    expect(checkin_page).to.be.true;
                });

                it('should successfully check people in', function *() {
                    var checked_in_user = yield nightmare
                        .goto(event_page)
                        .click('p.checkin-link > a')
                        .evaluate(function () {
                            return document.querySelector('div.rsvp.not_checked_in').getAttribute('data-user-id')
                        });

                    expect(checked_in_user).to.not.be.null;

                    var checkin_count = yield nightmare
                        .click('div.rsvp.not_checked_in')
                        .goto(event_page)
                        .evaluate(function () {
                            return document.querySelectorAll('div.checkins > div.checkin').length;
                        });

                    expect(checkin_count).to.be.above(amount_checkins);
                });

            });

            describe('Raffle Page', function () {

                it('should pick a winner', function *() {
                    var raffle_result = yield nightmare
                        .goto(event_page)
                        .type('body', '\u0020')
                        .wait('div.checkin.winner')
                        .visible('.checkin.winner > .name');

                    expect(raffle_result).to.be.true;
                });

                it('should pick different winners', function *() {
                    var first_result = yield nightmare
                        .goto(event_page)
                        .type('body', '\u0020')
                        .wait('div.checkin.winner')
                        .evaluate(function () {
                            return document.querySelector('.checkin.winner > .name').innerText
                        });

                    var dismiss_winner = yield nightmare
                        .type('body', '\u0020')
                        .exists('div.checkin.winner');

                    expect(dismiss_winner).to.be.false;

                    var second_result = yield nightmare
                        .goto(event_page)
                        .type('body', '\u0020')
                        .wait('div.checkin.winner')
                        .end()
                        .evaluate(function () {
                            return document.querySelector('.checkin.winner > .name').innerText
                        });

                    expect(second_result).to.not.be.equal(first_result);
                });
            })
        });

    });


});

//var array = Array.prototype.slice.call(list);

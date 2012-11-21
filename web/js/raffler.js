function Raffler(membersList, actionButton, clearButton, resultDiv) {

	this.participants     = membersList;
	this.resultDiv        = $(resultDiv);
	this.winnerAvailable  = false;

	this.currentCycle;
	this.callInterval;
	this.totalCycles;
	this.winner;
	this.wIntervalId;
	this.sIntervalId;

	// Bind Start Button
	$(actionButton).click(this.startBtnAction.bind(this));

	// Bind Clear Button
    $(clearButton).click(this.clearWinnersBtnAction.bind(this));
}

Raffler.prototype.start = function () {
    this.callInterval = 100;
    this.totalCycles = 100;
    this.currentCycle = 0;

    //Get a Winner
    this.selectWinner();

    //Kickoff Animation
    this.setAnimationInterval(this.callInterval);
    this.setWinnerInterval(this.callInterval, this.totalCycles);
};

Raffler.prototype.cycle = function()
{
	var participant;

	this.currentCycle++;

	// Animation: Select a participant and highlight him
	participant = this.randomParticipant();
	this.highlightParticipant(participant);

	// Adjust speed (slower towards end)
	this.adjustSpeed();
};

Raffler.prototype.adjustSpeed = function()
{
	var modifier, growthPct, linearMultiplier;

    modifier         = (this.currentCycle / this.totalCycles);
    growthPct        = 1 + modifier;
    linearMultiplier = 80 / (modifier * 100);

	// Update interval to new value
    this.callInterval = this.callInterval * growthPct + (linearMultiplier * modifier);

	// Set new interval
    clearInterval(this.sIntervalId);
	this.setAnimationInterval(this.callInterval);
};

Raffler.prototype.selectWinner = function()
{
    var min = 0, max, url;

    max = this.participants.length;

    url = "http://www.random.org/integers/?num=1&min="+min+"&max="+max+"&col=1&base=10&format=plain&rnd=new"

    $.get(url, function(data){
        this.winner = $(this.participants[parseInt(data)])[0];

		this.winnerAvailable = true;
    }.bind(this));
};

Raffler.prototype.showWinner = function()
{
    var $winner, html;

    //Still waiting for winner, animate some more
    if (this.winnerAvailable == undefined) {

		this.setWinnerInterval(this.callInterval, 10);

		return;
    }

	// Highlight Winner first
	this.highlightParticipant($(this.winner));

    //Show winner
    $winner = $(this.winner).clone();
    $winner.attr('src', $winner.attr('data-big-src'));
    $winner.removeClass('thumb').addClass('photo').removeClass('img-rounded');

    html = "<div class=\"winner\">"+ $winner[0].outerHTML +"<div class=\"name\">"+$winner.attr('alt')+"</div></div>";

    this.resultDiv.find('.modal-winner').html(html);
    this.resultDiv.find('#winner-id').val($winner.attr('data-id'));

    this.resultDiv.modal();

    clearInterval(this.sIntervalId);
	clearInterval(this.wIntervalId);
};

Raffler.prototype.randomParticipant = function()
{
	var rnd = Math.floor(Math.random() * this.participants.length);

	return this.participants[rnd];
};

Raffler.prototype.highlightParticipant = function(participant)
{

    var animateOn, animateOff,
        animtime = 50,
        delay = this.callInterval - (animtime * 2);

    animateOn = {
        borderColor: "#333333",
        backgroundColor: "#CC0000",
        opacity: 1
    };

    animateOff = {
        borderColor: "#FFF",
        backgroundColor: "#FFF",
        opacity: 1
    };

    $(participant).animate(animateOn, animtime).delay(delay).animate(animateOff, animtime);
};

Raffler.prototype.clearWinnersBtnAction = function (event) {
	event.preventDefault();
	this.resultDiv.html('').hide("fast");
};

Raffler.prototype.startBtnAction = function (event) {
	event.preventDefault();
	this.start();
};

Raffler.prototype.setAnimationInterval = function (interval) {
	this.sIntervalId = setInterval(this.cycle.bind(this), interval);
};

Raffler.prototype.setWinnerInterval = function (interval, totalCycles) {

    if (totalCycles == undefined) {
        totalCycles = 10;
    }

    this.wIntervalId = setInterval(this.showWinner.bind(this), interval * totalCycles);
};

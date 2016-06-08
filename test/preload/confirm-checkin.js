window.__nightmare = {};
__nightmare.ipc = require('ipc');

window.confirm = function(message, defaultResponse){
    if(message.includes('Checkin as')){
        return true;
    }

    return defaultResponse;
};

window.alert = function (message) {
    return true;
};

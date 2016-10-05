window.__nightmare = {};
__nightmare.ipc = require('electron').ipcRenderer;

window.confirm = function(message, defaultResponse){
    if(message.includes('Checkin as')){
        return true;
    }

    return defaultResponse;
};

window.alert = function (message) {
    return true;
};

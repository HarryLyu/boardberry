(function(BB){
    location.hash = '';
    window.top.scrollTo(0, 1);
    $.mobile.defaultPageTransition = 'flip';
    BB.templates = {};
    BB.classes = {};
    BB.user = {};
    BB.teams = {};
    BB.roomData = {};

    BB.subscribeOnChannel = function(data){
        if (!BB.isChannelInited) {
            BB.isChannelInited = true;
            console.log('Channel', data.channel, 'subscribed', data);

            BB.realplexor.setCursor(data.channel, data.channel_time);
            BB.realplexor.subscribe(data.channel, function(eventData, id) {
                console.log('CHANNEL EVENT', eventData);
                console.log('recieved channel data', eventData.eventName, eventData);

                if (BB.channelHandlers[eventData.eventName]) {
                    BB.channelHandlers[eventData.eventName](eventData.data);
                }
                else {
                    console.log('NO channel handler!', eventData.eventName);
                }
            });

            BB.realplexor.execute();
        }
    };

})(window.BB = {});
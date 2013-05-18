(function(BB){
    location.hash = '';

    BB.templates = {};
    BB.classes = {};
    BB.user = {};
    BB.teams = {};
    BB.roomData = {};

    BB.channelHandlers = {
        playerJoinedToTeam: BB.views.teams.playerJoinedToTeam_handler.bind(BB.views.teams),
        teamAdded: BB.views.teams.teamAdded_handler.bind(BB.views.teams),
        playerJoinedToRoom: BB.views.teams.playerJoinedToRoom_handler.bind(BB.views.teams),
        turnStarted: BB.views.turnStarted.initView.bind(BB.views.turnStarted),
        gameStarted: function(data){
            data.teams.forEach(function(elem){
               BB.teams[elem.id] = elem.players;
            });
        }
    };

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
    }
})(window.BB = {});
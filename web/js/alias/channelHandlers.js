BB.channelHandlers = {
    playerJoinedToTeam: BB.views.teams.playerJoinedToTeam_handler.bind(BB.views.teams),
    teamAdded: BB.views.teams.teamAdded_handler.bind(BB.views.teams),
    playerJoinedToRoom: BB.views.teams.playerJoinedToRoom_handler.bind(BB.views.teams),
    turnStarted: BB.views.turnStarted.initView.bind(BB.views.turnStarted),
    gameStarted: function(data){
        data.teams.forEach(function(elem){
            if (elem.players.length) {
                BB.teams[elem.id] = elem.players;
            }
        });
    },
    explanationStarted: BB.views.explanationStarted.initView.bind(BB.views.explanationStarted),
    explanationFinished: BB.views.explanationFinished.initView.bind(BB.views.explanationFinished),
    resultUpdated: BB.views.explanationFinished.updateResult.bind(BB.views.explanationFinished)
};
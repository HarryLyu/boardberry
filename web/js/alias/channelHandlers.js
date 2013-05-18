BB.channelHandlers = {
    playerJoinedToTeam: BB.views.teams.playerJoinedToTeam_handler.bind(BB.views.teams),
    playerJoinedToRoom: BB.views.teams.playerJoinedToRoom_handler.bind(BB.views.teams),
    teamAdded: BB.views.teams.teamAdded_handler.bind(BB.views.teams)
};
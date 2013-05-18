BB.classes.JoinView = Class.extend({
    loc: {
        input: '[data-room-id]',
        joinBtn: '[data-game-action=join-room]'
    },

    init: function (params){
        this.params = params;
        this.root = $(params.root);
        this.private_assignEvents();
    },


    private_assignEvents: function (){
        var self = this;
        this.root
            .on('click', this.loc.joinBtn, function(){
                var roomId = $(self.loc.input).val();

                if (!roomId) {
                    alert('Please type game number!');
                    return;
                }

                if (!/\d{8}/.test(roomId)) {
                    alert('Game number must contain 8 numbers!');
                    return;
                }

                $.post('/api/room/' + roomId,{
                    action: 'join-room',
                    user: BB.user.id
                }, function (data) {
                    BB.views.teams.render(data.data);
                });

            });
    },

    render: function (data){
        this.root.html(tmpl('tplJoin', {data:data}));
        $.mobile.navigate('#join');
    },

    update: function (data){
        this.root.html(tmpl('tplJoin', {data:data}));
    },

    getData: function (){
        return this.params;
    }
});

BB.classes.TeamsView = Class.extend({
    loc: {
        teamItem: '[data-team-item]'
    },

    init: function (params){
        this.params = params;
        this.root = $(params.root);
        this.private_assignEvents();
    },


    private_assignEvents: function () {
        var self = this;

        this.root
            .on('click', this.loc.teamItem, function(){
                console.log('click on join team');

                $.post('/api/room/' + self.data.id,{
                    action: 'join-team',
                    team: $(this).data('team-item'),
                    user: BB.user.id
                }, function(data){
                    console.log('join team response', data);
                });
            })
            .on('click', '[data-game-action="add-team"]', function () {
                console.log('add team click');

                $.post('/api/room/' + self.data.id,{
                    action: 'add-team',
                    user: BB.user.id
                }, function (data) {
                    console.log('add team response', data);
                });
            })
            .on('click', '[data-game-action="start-game"]', function () {
                console.log('start game click');

                $.post('/api/room/' + self.data.id,{
                    action: 'start-game'
                }, function (data) {
                    console.log('start game response', data);
                });
            });
    },

    render: function (data) {
        this.data = data;

        if (!this.isChannelInited) {
            console.log('Channel', this.data.channel, 'subscribed', this.data);

            BB.realplexor.setCursor(this.data.channel, this.data.channel_time);
            BB.realplexor.subscribe(this.data.channel, function(data, id) {
                console.log('CHANNEL EVENT', data);
                console.log('recieved channel data', data.eventName, data);

                if (BB.channelHandlers[data.eventName]) {
                    BB.channelHandlers[data.eventName](data.data);
                }
                else {
                    console.log('NO channel handler!', data.eventName);
                }
            });

            BB.realplexor.execute();
        }

        this.root.html(tmpl('tplTeams', {data: data, me: BB.user}));
        $.mobile.navigate('#teams');
    },

    playerJoinedToTeam_handler: function (data) {
        this.root.find('[data-user-item="' + data.playerId + '"]').remove();

        $('<img/>')
            .attr('src', 'https://graph.facebook.com/' + data.playerId + '/picture?type=square')
            .attr('data-user-item', data.playerId)
            .appendTo(this.root.find('[data-team-item="' + data.teamId + '"]'));
    },

    playerJoinedToRoom_handler: function (data) {
        this.root.find('[data-players-count]').html(data.playerCount);
    },

    teamAdded_handler: function (data) {
        $('<div/>')
            .attr('data-team-item', data.teamId)
            .addClass('team-item team-item-' + data.teamId)
            .appendTo(this.root.find('[data-team-container]'));
    },

    update: function (data){
        this.root.html(tmpl('tplTeams', {data:data}));
    },

    getData: function (){
        return this.params;
    }
});

BB.classes.TurnPrepareView = Class.extend({
    loc: {
        imReadyBtn: '[data-game-action="turn-start"]'
    },

    init: function (params){
        this.params = params;
        this.root = $(params.root);
        this.private_assignEvents();
    },


    private_assignEvents: function (){
        this.root.on('click', this.loc.imReadyBtn, function(){
            $.post('/api/room/' + BB.views.teams.data.id,{
                action: 'turn-start',
                user: BB.user.id
            }, function(data){
                console.log(data);
            });
        })
    },

    render: function (data){
        this.root.html(tmpl('tplTurnPrepare', {explain: data.explain, me: BB.user, guess: data.guess}));
        $.mobile.navigate('#turn-prepare');
    },

    update: function (data){
        this.root.html(tmpl('tplTurnPrepare', {explain: data.explain, me: BB.user, guess: data.guess}));
    },

    getData: function (){
        return this.params;
    }
});

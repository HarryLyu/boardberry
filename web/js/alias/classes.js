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
        this.root.on('click', this.loc.joinBtn, function(){
            $.post('/api/room',{
                action: 'join-room',
                id: $(self.loc.input).val(),
                user: BB.user.id
            }, function(data){
                BB.views.teams.render(data);
            });

        })
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


    private_assignEvents: function (){
        this.root.on('click', this.loc.teamItem, function(){
            $.post('/api/room',{
                action: 'join-team',
                id: $(this).data('team-item'),
                user: BB.user.id
            }, function(data){
                console.log(data);
            });

        })
    },

    render: function (data){
        this.root.html(tmpl('tplTeams', {data:data}));
        $.mobile.navigate('#teams');
    },

    update: function (data){
        this.root.html(tmpl('tplTeams', {data:data}));
    },

    getData: function (){
        return this.params;
    }
});

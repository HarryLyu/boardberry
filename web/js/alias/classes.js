BB.classes.JoinView = Class.extend({
    loc: {
        input: '[data-room-id]'
    },

    init: function (params){
        this.params = params;
        this.root = $(params.root);
        this.private_assignEvents();
    },


    private_assignEvents: function (){
        var self = this;
        this.root.on('click', '[data-game-action=join-room', function(){
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

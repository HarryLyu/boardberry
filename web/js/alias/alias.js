(function(BB){

    BB.activeState = "";

    BB.changeState = function (data){
        var state = data.state;

        if (BB.activeState === state){
            BB.views[state].update();
        } else {
            BB.activeState = state;
            BB.views[state].render();
        }
    };


    BB.views = {
        join: new BB.classes.JoinView({root: '[data-view-name=join]'}),
        teams: new BB.classes.TeamsView({root: '[data-view-name=teams]'}),
        turnPrepare: new BB.classes.TurnPrepareView({root: '[data-view-name=turn-prepare]'})
    };

    $(document.body).on('click', '[data-game-action="create"]', function () {
        FBApp.login(function (authData, userProfile) {
            $.post(
                '/api/user', {
                    auth: authData,
                    user: userProfile
                },
                function (response) {
                    if (response.user.userID) {
                        BB.user.id = response.user.userID
                    }
                    $.post(
                        '/api/room',
                        {
                            action: 'create',
                            owner: BB.user.id
                        },
                        function (roomData) {
                            console.log('room created', roomData);
                            BB.views.teams.render(roomData.data);
                        }
                    );
                }
            );
        });
    }).on('click', '[data-game-action="join"]', function () {
        FBApp.login(function (authData, userProfile) {
            $.post(
                '/api/user', {
                    auth: authData,
                    user: userProfile
                },
                function (response) {
                    if (response.user.userID) {
                        BB.user.id = response.user.userID
                    }
                    BB.views.join.render();
                }
            );

        });
    });

    BB.channelHandler = function (data, id) {
        data =  JSON.parse(data);

    };

    BB.realplexor = new Dklab_Realplexor(
        "http://comet." + location.host + "/",
        "BB"
    );
})(BB);
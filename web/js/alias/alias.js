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
        teams: new BB.classes.TeamsView({root: '[data-view-name=teams]'})
    };


    $(document.body).on('click', '[data-game-action="create"]', function () {
        FBApp.login(function (authData, userProfile) {
            $.post(
                '/api/user', {
                    auth: authData,
                    user: userProfile
                },
                function (response) {
                    if (response.userID) {
                        BB.user.id = response.user.userID
                    }
                    $.post(
                        '/api/room',
                        {
                            action: 'create',
                            owner: 'TODO ownerId'
                        },
                        function (roomData) {
                            BB.views.teams.render(roomData);
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
                    if (response.userID) {
                        BB.user.id = response.user.userID
                    }
                    $.post(
                        '/api/room',
                        {
                            action: 'join',
                            owner: 'TODO ownerId'
                        },
                        function (roomData) {
                            BB.views.join.render(roomData);
                        }
                    );
                }
            );

        });
    });

    BB.teamColors = [
        '#0000FF',
        '#8A2BE2',
        '#7FFF00',
        '#FF7F50',
        '#6495ED',
        '#DC143C',
        '#006400',
        '#8B008B'
    ];
})(BB);
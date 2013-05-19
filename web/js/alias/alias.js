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
        teams: new BB.classes.TeamsView({root: '[data-view-name=teams]'}),
        turnStarted: new BB.classes.TurnStartedView({root: '[data-view-name=turn-started]'}),
        explanationStarted: new BB.classes.ExplanationStartedView({root:'[data-view-name=explanation-started]'}),
        explanationFinished: new BB.classes.ExplanationFinishedView({root: '[data-view-name=explanation-finished]'}),
        turnFinished: new BB.classes.TurnFinishedView({root: '[data-view-name=turn-finished]'}),
        gameFinished: new BB.classes.GameFinishedView({root: '[data-view-name=game-finished]'})
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
                            BB.views.teams.initView(roomData.data);
                        }
                    );
                }
            );
        });
    }).on('click', '[data-game-action="join"]', function () {
            var roomId = $('[data-room-id]').val();

            if (!roomId) {
                alert('Введите номер игры!');
                return;
            }

            if (!/\d{8}/.test(roomId)) {
                alert('Номер игры должен состоять из восьми цифр!');
                return;
            }

            FBApp.login(function (authData, userProfile) {
                $.post(
                    '/api/user', {
                        auth: authData,
                        user: userProfile
                    },
                    function (response) {
                        if (response.user.userID) {
                            BB.user.id = response.user.userID;
                            BB.user.name = response.user.name;
                        }

                        $.post('/api/room/' + roomId, {
                            action: 'join-room',
                            user: BB.user.id
                        }, function (data) {
                            if (data.error) {
                                alert(data.error);
                                return;
                            }
                            BB.views.teams.initView(data.data);
                        });
                    }
                );
            });
    });

    BB.realplexor = new Dklab_Realplexor(
        "http://comet." + location.host + "/",
        "BB"
    );
})(BB);
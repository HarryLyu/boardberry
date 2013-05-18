(function(BB){

    BB.stateToViewConfig = {
        1 : 'join'
    };

    BB.changeState = function (data){
        var view = BB.stateToViewConfig[data.stateId],
            innerHTML = BB.templates.alias[view](data);

        BB.showView(view, innerHTML);
    };


    $(document.body).on('click', '[data-game-action="create"]', function () {
        FBApp.login(function (authData, userProfile) {
            $.post(
                '/api/user', {
                    auth: authData,
                    user: userProfile
                },
                function (response) {
                    $.post(
                        '/api/room',
                        {
                            action: 'create',
                            owner: 'TODO ownerId'
                        },
                        function (roomData) {
                            console.log('room recieved', roomData);
                        }
                    );
                }
            );
        });
    }).on('click', '[data-game-action="join"]', function () {
            var mock = {
                stateId : 1,
                moreInfo: 'dddddd'
            };

            BB.changeState(mock);

//        FBApp.login(function (authData, userProfile) {
//            console.log()
//        });
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
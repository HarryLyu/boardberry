(function(){

    $(document.body).on('click', '[data-game-action="create"]', function () {
        console.log('click');

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
                            action: 'create'
                        },
                        function (roomData) {
                            console.log('room recieved', roomData);
                        }
                    );
                }
            );
        });
    });

    $(document.body).on('click', '[data-game-action="join"]', function () {
        console.log('click');

        FBApp.login(function (authData, userProfile) {
            console.log()
        });
    });
})();
(function(){

    window.fbAsyncInit = function() {
        FB.init({
            appId      : '163743487134776',                        // App ID from the app dashboard
            channelUrl : 'http://igor.boardberry.me/channel.html', // Channel file for x-domain comms
            status     : true,                                 // Check Facebook Login status
            xfbml      : true                                  // Look for social plugins on the page
        });

    };

    // Load the SDK asynchronously
    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));


    window.FBApp = {
        login: function (callback) {


            FB.getLoginStatus(function(response) {
                if (response.status === 'connected') {
                    console.log('already auth', response);

                    FB.api('/me', function(userData) {
                        console.log('Good to see you, ' + userData.name + '.', userData);
                        callback && callback(response, userData);
                    });
                } else {
                    FB.login(function(authData) {
                        console.log('receive first response');
                        if (authData.authResponse) {
                            console.log('Welcome!  Fetching your information.... ', authData.authResponse);
                            FB.api('/me', function(response) {
                                console.log('Good to see you, ' + response.name + '.', response);
                                callback && callback(authData, response);
                            });
                        } else {
                            console.log('no auth');
                            alert('You should authorize if you wanna play!');
                        }
                    });
                }
            });
        }
    }
})();
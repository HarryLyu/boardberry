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
                    alert('Введите номер игры!');
                    return;
                }

                if (!/\d{8}/.test(roomId)) {
                    alert('Номер игры должен состоять из восьми цифр!');
                    return;
                }

                $.post('/api/room/' + roomId,{
                    action: 'join-room',
                    user: BB.user.id
                }, function (data) {
                    BB.views.teams.initView(data.data);
                });

            })
    },

    initView: function(data){
        this.private_render(data);
    },

    private_render: function (data){
        this.root.html(tmpl('tplJoin', {data:data}));
        $.mobile.navigate('#join');
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

                $.post('/api/room/' + BB.roomData.id,{
                    action: 'join-team',
                    team: $(this).data('team-item'),
                    user: BB.user.id
                }, function(data){
                    console.log('join team response', data);
                });
            })
            .on('click', '[data-game-action="add-team"]', function () {
                console.log('add team click');

                $.post('/api/room/' + BB.roomData.id,{
                    action: 'add-team',
                    user: BB.user.id
                }, function (data) {
                    console.log('add team response', data);
                });
            })
            .on('click', '[data-game-action="start-game"]', function () {
                console.log('start game click');

                $.post('/api/room/' + BB.roomData.id,{
                    action: 'start-game'
                }, function (data) {
                    console.log('start game response', data);
                });
            });
    },

    initView: function(data){
        BB.roomData = data;
        BB.subscribeOnChannel(data);
        this.private_render(data);
    },

    private_render: function (data){
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
    }
});

BB.classes.TurnStartedView = Class.extend({
    loc: {
        imReadyBtn: '[data-game-action="start-explanation"]'
    },

    init: function (params){
        this.params = params;
        this.root = $(params.root);
        this.private_assignEvents();
    },


    private_assignEvents: function (){
        this.root.on('click', this.loc.imReadyBtn, function(){
            $.post('/api/room/' + BB.roomData.id,{
                action: 'start-explanation',
                user: BB.user.id
            }, function(data){
                console.log(data);
            });
        })
    },

    initView: function(data){
        this.private_render(data);
    },

    private_render: function (data){
        this.root.html(tmpl('tplTurnStarted', {explainer: data.explainer, me: BB.user, team: BB.teams[data.activeTeamId]}));
        $.mobile.navigate('#turn-started');
    }
});

BB.classes.ExplanationStartedView = Class.extend({
    loc: {
        time: '[data-time-container]',
        word: '[data-word-container]',
        skipBtn: '[data-skip-btn]',
        answerBtn: '[data-answer-btn]',
        skippedCount: '[data-skipped-count]',
        answeredCount: '[data-answered-count]'
    },

    init: function (params){
        this.params = params;
        this.root = $(params.root);
        this.private_assignEvents();
    },


    private_assignEvents: function (){
        var right = this.private_answer.bind(this, true),
            fail = this.private_answer.bind(this, false);

        this.root
                .on('click', this.loc.answerBtn, right)
                .on('click', this.loc.skipBtn, fail)
                .on('swipeleft', fail)
                .on('swiperight', right)
    },

    initView: function(data){
        console.log('ExplanationStartedView initView', data);
        this.data = data;
        this.currentWordIndex = 0;
        this.answeredCount = 0;
        this.skippedCount = 0;
        this.wordAnswers = [];

        this.private_render(data);
        this.private_initTimer();
        this.private_loadWord();
    },

    private_answer: function (isRight) {
        if (BB.user.id != BB.explainer.id){
            return false
        }
        this.wordAnswers.push(isRight ? 1 : 0);
        this.currentWordIndex += 1;
        if (isRight) {
            this.answeredCount += 1;
        } else {
            this.skippedCount += 1;
        }

        this.private_updateAnswersCount();
        this.private_loadWord();
    },

    private_updateAnswersCount: function () {
        $(this.loc.answeredCount).html(this.answeredCount);
        $(this.loc.skippedCount).html(this.skippedCount);
    },

    private_loadWord: function () {
        $(this.loc.word).html(this.data.words[this.currentWordIndex]);
    },

    private_render: function (data){
        BB.explainer = data.explainer;
        if (data.explainer.id == BB.user.id){
            console.log('render explanation for Explainer');
            this.root.html(tmpl('tplTurnExplain', {}));
        } else {
            console.log('render explanation for Waiter');
            this.root.html(tmpl('tplTurnWait', {explainer: data.explainer, me: BB.user, team: BB.teams[data.activeTeamId]}));
        }
        $.mobile.navigate('#explanation-started');
    },

    private_initTimer: function(){
        console.log('init timer');
        var self = this,
            $timer = $(this.loc.time),
            startTime = new Date().getTime(),
            maxDiff = 10 * 1000;

        this.timerInterval = setInterval(function () {
            var currentTime = new Date().getTime(),
                deltaTime = currentTime - startTime,
                timeToShow = maxDiff - deltaTime,
                seconds = Math.round(timeToShow / 1000),
                milliSeconds = timeToShow % 1000;

            $timer.html('00:' + seconds + ':' + milliSeconds);

            if (deltaTime > maxDiff) {
                clearInterval(self.timerInterval);
                self.private_onEndTimer();
            }
            else {
                $timer.html('00:' + Math.max(seconds, 0) + ':' + Math.max(milliSeconds, 0));
            }
        }, 100)
    },

    private_onEndTimer: function (){
        console.log('timer end');

        if (this.data.explainer.id == BB.user.id){
            $.post('/api/room/' + BB.roomData.id, {
                action: 'finish-explanation',
                words: this.wordAnswers
            },
            function (data){
                console.log('answers save result', data);
            })
        }
    }
});

BB.classes.ExplanationFinishedView = Class.extend({
    loc: {
        toggleResultBtn: '[data-word-id]'
    },

    init: function (params){
        this.params = params;
        this.root = $(params.root);
        this.private_assignEvents();
    },


    private_assignEvents: function (){
        this.root
            .on('click', '[data-word-id]', function () {
                $.post('/api/room/' + BB.roomData.id,{
                    action: 'edit-result',
                    word_id: $(this).data('word-id')
                },
                function (data) {
                    console.log ('edit result response ', data)
                });

                return false;
            })
            .on('click', '[data-game-action="save-results"]', function () {
                $.post('/api/room/' + BB.roomData.id,{
                    action: 'save-results'
                },
                function (data) {
                    console.log ('save result response ', data)
                })
            });
    },

    initView: function(data){
        this.private_render(data);
    },

    private_render: function (data){
        this.root.html(tmpl('tplTurnResult', {words: data.words, me: BB.user, explainer: data.explainer, team: BB.teams[data.activeTeamId]}));
        $.mobile.navigate('#explanation-finished');
    },

    updateResult: function (wordData) {
        this.root.find('[data-word-id=' + wordData.id + ']').toggleClass('correct', wordData.result == 1);
    }
});


BB.classes.TurnFinishedView = Class.extend({
    loc: {
        nextTurnBtn: '[data-game-action="next-turn"]'
    },

    init: function (params){
        this.params = params;
        this.root = $(params.root);
        this.private_assignEvents();
    },

    private_assignEvents: function (){
        this.root
            .on('click', this.loc.nextTurnBtn, function () {
                $.post('/api/room/' + BB.roomData.id,{
                        action: 'next-turn'
                    },
                    function (data) {
                        console.log ('next turn response ', data)
                    });

                return false;
            })
    },

    initView: function(data){
        this.private_render(data);
    },

    private_render: function (data){
        this.root.html(tmpl('tplTurnFinished', {
            data: data,
            teams: BB.teams,
            me: BB.user,
            explainer: BB.explainer
        }));
        $.mobile.navigate('#turn-finished');
    }
});

BB.classes.GameFinishedView = Class.extend({
    loc: {

    },

    init: function (params){
        this.params = params;
        this.root = $(params.root);
        this.private_assignEvents();
    },

    private_assignEvents: function (){
        this.root
            .on('click', this.loc.nextTurnBtn, function () {
                $.post('/api/room/' + BB.roomData.id,{
                        action: 'next-turn'
                    },
                    function (data) {
                        console.log ('next turn response ', data)
                    });

                return false;
            })
    },

    initView: function (data) {
        this.private_render(data);
    },

    private_render: function (data) {
        this.root.html(tmpl('tplGameFinished', {
            data: data,
            teams: BB.teams,
            me: BB.user,
            explainer: BB.explainer
        }));
        $.mobile.navigate('#turn-finished');
    }
});

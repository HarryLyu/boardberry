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

        this.root.find('[data-game-action="start-game"]').toggle(!!data.isGameCanBeStarted);
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
        progressBar: '[data-time-progress-bar]',
        word: '[data-word-container]',
        wordText: '[data-word-text]',
        skipBtn: '[data-skip-btn]',
        answerBtn: '[data-answer-btn]',
        skippedCount: '[data-skipped-count]',
        answeredCount: '[data-answered-count]'
    },

    init: function (params) {
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

        document.getElementById('audio_gong').play();
    },

    private_answer: function (isRight) {
        if (BB.user.id != BB.explainer.id){
            return false
        }
        var $word = $(this.loc.word)
        this.wordAnswers.push(isRight ? 1 : 0);
        this.currentWordIndex += 1;

        if (isRight) {
            $word.css('left', '500px');
            setTimeout(function(){
                $word.css('display', 'none');
                $word[0].offsetHeight;
                $word.css('left', '0');
                $word.css('display', 'block')
            }, 400);
            this.answeredCount += 1;
            document.getElementById('audio_correct').play();
        } else {
            $word.css('left', '-500px');
            setTimeout(function(){
                $word.css('display', 'none');
                $word[0].offsetHeight;
                $word.css('left', '0');
                $word.css('display', 'block')
            }, 400);

            this.skippedCount += 1;
            document.getElementById('audio_incorrect').play();
        }

        this.private_updateAnswersCount();
        this.private_loadWord();
    },

    private_updateAnswersCount: function () {
        if (this.answeredCount) {
            $(this.loc.answeredCount).html('+' + this.answeredCount);
        }
        if (this.skippedCount) {
            $(this.loc.skippedCount).html('-' + this.skippedCount);
        }
    },

    private_loadWord: function () {
        $(this.loc.wordText).html(this.data.words[this.currentWordIndex]);
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
            $pBar = $(this.loc.progressBar),
            startTime = new Date().getTime(),
            maxDiff = 60 * 1000;

        this.timerInterval = setInterval(function () {
            var currentTime = new Date().getTime(),
                deltaTime = currentTime - startTime,
                timeToShow = maxDiff - deltaTime,
                seconds = Math.round(timeToShow / 1000),
                milliSeconds = (timeToShow % 1000) / 1000,
                resString = "";


            if (deltaTime > maxDiff) {
                clearInterval(self.timerInterval);
                self.private_onEndTimer();
            }
            else {
                seconds = (seconds<10) ? '0' + seconds : seconds;
                milliSeconds = milliSeconds.toFixed(2).toString().slice(2,4);
                resString = '00:' + seconds + ':' + milliSeconds;
                resString.slice(resString.length-1);
                $timer.html(resString);
                $pBar.css('width', (100 - (deltaTime/maxDiff)*100) + '%')
            }
        }, 100);
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
        document.getElementById('audio_show_result').play();
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

        var chartData = [];

        data.forEach(function (item) {
            chartData.push({
                fillColor: ChartColors[item.id],
                data: [item.position || 1]
            })
        });

        var charts = document.getElementByClassName("resultsChart");

        charts.forEach(function (element) {
            new Chart(element.getContext("2d")).Bar({
                labels : [""],
                datasets : chartData
            }, ChartOptions);
        });
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

        var winnerTeam = {position: 0};

        data.forEach(function (teamItem) {
            if (teamItem.position > winnerTeam.position) {
                winnerTeam = teamItem
            }
        });

        this.private_render({
            winner: winnerTeam
        });
    },

    private_render: function (data) {
        this.root.html(tmpl('tplGameFinished', {
            winner: data.winner,
            team: BB.teams[data.winner.id]
        }));

        $.mobile.navigate('#game-finished');
    }
});

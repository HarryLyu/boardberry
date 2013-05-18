(function(BB){
    BB.templates = {};

    BB.showView  = function (viewName, innerHTML) {
        var m = $.mobile,
            name = '#'+viewName;

        if (innerHTML){
            $(name).html(innerHTML);
        }

        m.navigate(name);
        return viewName;
    };
})(window.BB = {});
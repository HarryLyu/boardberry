<html>
    <head>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

        <script type="text/javascript" src="http://comet.stas.boardberry.me/?identifier=SCRIPT"></script>
        <script type="text/javascript">
            $(document).ready(function() {
            var realplexor = new Dklab_Realplexor(
                    "http://comet.stas.boardberry.me/",
                    "bb"
            );


            realplexor.setCursor("BroadCast", <?=time();?>;

            realplexor.subscribe("BroadCast", function(data, id) {
                $('#first').append(data+"<br>");
            });
            realplexor.execute();
            });
        </script>
    </head>
    <body>
    <div id="first"></div>
    </body>
</html>
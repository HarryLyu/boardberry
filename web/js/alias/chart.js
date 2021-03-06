var ChartOptions = {
    //Boolean - If we show the scale above the chart data
    scaleOverlay : false,

    //Boolean - If we want to override with a hard coded scale
    scaleOverride : true,

    //** Required if scaleOverride is true **
    //Number - The number of steps in a hard coded scale
    scaleSteps : 10,
    //Number - The value jump in the hard coded scale
    scaleStepWidth : 5,
    //Number - The scale starting value
    scaleStartValue : 0,

    //String - Colour of the scale line
    scaleLineColor : "rgba(0,0,0,.1)",

    //Number - Pixel width of the scale line
    scaleLineWidth : 1,

    //Boolean - Whether to show labels on the scale
    scaleShowLabels : true,

    //Interpolated JS string - can access value
    scaleLabel : "<%=Math.round(value)%>",

    //String - Scale label font declaration for the scale label
    scaleFontFamily : "'Arial'",

    //Number - Scale label font size in pixels
    scaleFontSize : 12,

    //String - Scale label font weight style
    scaleFontStyle : "normal",

    //String - Scale label font colour
    scaleFontColor : "#666",

    ///Boolean - Whether grid lines are shown across the chart
    scaleShowGridLines : true,

    //String - Colour of the grid lines
    scaleGridLineColor : "rgba(0,0,0,.05)",

    //Number - Width of the grid lines
    scaleGridLineWidth : 1,

    //Boolean - If there is a stroke on each bar
    barShowStroke : true,

    //Number - Pixel width of the bar stroke
    barStrokeWidth : 2,

    //Number - Spacing between each of the X value sets
    barValueSpacing : 5,

    //Number - Spacing between data sets within X values
    barDatasetSpacing : 1,

    //Boolean - Whether to animate the chart
    animation : true,

    //Number - Number of animation steps
    animationSteps : 60,

    //String - Animation easing effect
    animationEasing : "easeOutQuart",

    //Function - Fires when the animation is complete
    onAnimationComplete : null
};

var ChartColors = [
    '#08c',
    '#5bc0de',
    '#62c462',
    '#fbb450',
    '#ee5f5b',
    '#08c',
    '#5bc0de',
    '#62c462',
    '#fbb450',
    '#fbb450'
];

var drawCharts = function (chartData) {
    var charts = document.getElementsByClassName("resultsChart");

    for (var i = 0, l = charts.length; i < l; i++) {
        var chart = charts[i];
        new Chart(chart.getContext("2d")).Bar({
            labels : [""],
            datasets : chartData
        }, ChartOptions);
    }
};
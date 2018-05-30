<?php 
use LupeCode\phpTraderNative\Trader as Trader;

require_once 'vendor/autoload.php';

$vendorDir = dirname(dirname(__FILE__));

ob_start();
include $vendorDir.'/poloniex-api/controller/connect_db.php';
ob_end_clean();

$pair=$_GET["pair"];
// echo $pair;

// select all columns from database
$sql = 'SELECT pair, date_time, close, high, low, open FROM Data WHERE pair="'.$pair.'"';

// $sql = 'SELECT pair, date_time, close, high, low, open FROM Data WHERE pair="BTC_AMP"';

$result = $conn->query($sql);  


if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) { 
        $date_time[] = $row['date_time'];
        $formated_date[] = gmdate("d-m-Y H:i:s", $row['date_time']);
        $high[] = $row['high'];
        $close[] = $row['close'];
        $low[] = $row['low'];
        $open[] = $row['open'];
    }
} else {
    echo "0 results";
}


//Moving Avarage Indicator
$ma= Trader::ma($close, 100);
$date_ma = [];
foreach ($ma as $key => $value) {
	$date_ma[$key] = $date_time[$key];
}

$macd_get = Trader::macd($close);
$macd = $macd_get['MACD'];
$macd_sig = $macd_get['MACDSignal'];
$macd_hist = $macd_get['MACDHist'];

foreach ($macd as $key => $value) {
	$date_macd[$key] = gmdate("d-m-Y H:i:s", $date_time[$key]);
}
?>



<head>
	<link rel="stylesheet" href="css/main.css">
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
</head>
<body>
<!-- Plotly chart will be drawn inside this div -->
<div id="graph"></div>
<script>
	var date_time= <?php echo json_encode($date_time); ?>;
	var close= <?php echo json_encode($close); ?>;
	var high= <?php echo json_encode($high); ?>;
	var low= <?php echo json_encode($low); ?>;
	var open= <?php echo json_encode($open); ?>;
	var ma= Object.values(<?php echo json_encode($ma); ?>);
	var date_ma= Object.values(<?php echo json_encode($date_ma); ?>);
	var macd = Object.values(<?php echo json_encode($macd); ?>);
	var macd_sig = Object.values(<?php echo json_encode($macd_sig); ?>);
	var macd_hist = Object.values(<?php echo json_encode($macd_hist); ?>);
	var date_macd = Object.values(<?php echo json_encode($date_macd); ?>);
	var formated_date = Object.values(<?php echo json_encode($formated_date); ?>);
	var pair = <?php echo json_encode($pair); ?>;


	var trace1 = {
		x: formated_date,
		close: close,
		high: high,
		low: low,
		open: open,

		// cutomise colors
		increasing: {line: {color: 'green'}},
		decreasing: {line: {color: 'red'}},

		type: 'candlestick',
		xaxis: 'x1',
		yaxis: 'y1',
		hoverlabel:{bgcolor:'black'},
		name: '',
	};



	// var trace2= {
	//     x: date_ma,
	//     y: ma,
	//     type: 'scatter',
	//     xaxis: 'x',
	//  	yaxis: 'y1',
	//  	name: 'ma',
	//  	hoverinfo: 'skip',
	//  	line: {color:'blue'},
	//  	textfont:{size:100},
	//  	// xcalendar: 'julian',
	//   };

	  	var trace3= {
	    x: date_macd,
	    y: macd,
	    type: 'scatter',
	    xaxis: 'x',
	 	yaxis: 'y2',
	 	hoverinfo: 'none',
	 	line:{color:'green'},
	  };

	  	 var trace4= {
	    x: date_macd,
	    y: macd_sig,
	    type: 'scatter',
	    xaxis: 'x',
	 	yaxis: 'y2',
	 	hoverinfo: 'none',
	 	line:{color:'red'},
	  };

	  	var trace5= {
	    x: date_macd,
	    y: macd_hist,
	    type: 'bar',
	    xaxis: 'x',
	 	yaxis: 'y2',
	 	hoverinfo: 'none',
	 	marker:{color: macd_hist, colorscale: [[0, 'red'], [1, 'green']], cauto:true},

	  };

	var data=[trace1, trace3, trace4, trace5];

	var layout = {
	plot_bgcolor: 'black',
	paper_bgcolor: 'black',
	legend: {traceorder: 'reversed'},
	titlefont:{color:'white'},
	dragmode: 'zoom',
	showlegend: false,
	title: pair,
	hovermode: 'closest',
	spikedistance:2000,
	hoverdistance: 4000,
	xside: 'top plot',
	xaxis: {domain: [0,1], rangeslider: {visible: false}, ticks:'outside', side: 'bottom', anchor: 'y2', showspickes:true, spikemode: 'toaxis+across+marker', spikecolor:'white', spikedash:'solid', spikesnap:'data', tickangle:0, dtick:20, tickangle:30, tickfont:{size:9}},
	 // type:'date', tickformat:'%-d/%-m/%Y'
	yaxis: {domain: [0.5,1],title: 'Price',exponentformat:'none', showline:true},
	xaxis2: {domain: [0,1],rangeslider: {visible: false},ticks: 'outside',side: 'bottom', anchor: 'y2'},
	yaxis2: {domain: [0,0.2],title: 'MACD', exponentformat:'none', showticklabels:false, showline:true},
	};


	var resize= Plotly.plot('graph', data, layout, {displayModeBar: false});

	window.onresize = function() {
    console.log('ok');
};


</script>
</body>
</html>

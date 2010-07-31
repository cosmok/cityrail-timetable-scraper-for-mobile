<?php
require dirname(__FILE__) . '/stations.php';
?>
<html>
<head>
<title>Mobile Timetable</title>
<style>
body {
	font:normal 76%/150% "Lucida Grande", "Lucida Sans Unicode", Verdana, Arial, Helvetica, sans-serif;
    overflow-x:hidden;
}
/*
Coffee with milk
Table design by Roger Johansson, 456 Berea Street
www.456bereastreet.com
================================================*/
table {
	border-collapse:separate;
	border-spacing:0;
	margin:0 0 1em;
	color:#000;
	}
table a {
	color:#523A0B;
	text-decoration:none;
	border-bottom:1px dotted;
	}
table a:visited {
	color:#444;
	font-weight:normal;
	}
table a:visited:after {
	content:"\00A0\221A";
	}
table a:hover {
	border-bottom-style:solid;
	}
thead th,
thead td,
tfoot th,
tfoot td {
	border:1px solid #523A0B;
	border-width:1px 0;
	background:#EBE5D9;
	}
th {
	font-weight:bold;
	line-height:normal;
	padding:0.25em 0.5em;
	text-align:left;
	}
tbody th,
td {
	padding:0.25em 0.5em;
	text-align:left;
	vertical-align:top;
	}
tbody th {
	font-weight:normal;
	white-space:nowrap;
	}
tbody th a:link,
tbody th a:visited {
	font-weight:bold;
	}
tbody td,
tbody th {
	border:1px solid #fff;
	border-width:1px 0;
	}
tbody tr.odd th,
tbody tr.odd td {
	border-color:#EBE5D9;
	background:#F7F4EE;
	}
tbody tr:hover td,
tbody tr:hover th {
	background:#ffffee;
	border-color:#523A0B;
	}
caption {
	font-family:Georgia,Times,serif;
	font-weight:normal;
	font-size:1.4em;
	text-align:left;
	margin:0;
	padding:0.5em 0.25em;
	}
table.timeTable {
width:100%;
border-bottom: 1px dotted #000;
}
table.timeTable tr th{
    text-align: left;
}
table.timeTable tr td.station {
width:75%;
}
table.timeTable tr td.station span{
    text-align:left;
}
table.timeTable tr td.platform {
width:5%;
}
table.timeTable tr td.platform span{
    float:right;
}
table.timeTable tr td.time {
width:20%;
}
table.timeTable tr td.time span{
float:right;
}
div#wrapper {
}
form p {
height:25px;
}

form label {
width: 50px;
line-height:25px;
float: left;
}
.white {
background:-moz-linear-gradient(center top , #FFFFFF, #EDEDED) repeat scroll 0 0 transparent;
background: -webkit-gradient(linear, left top, left bottom, from(#fff), to(#ededed));
filter:  progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#ededed');
border:1px solid #B7B7B7;
color:#606060;
}
.button {
-moz-border-radius:0.5em 0.5em 0.5em 0.5em;
-moz-box-shadow:0 1px 2px rgba(0, 0, 0, 0.2);
-webkit-border-radius: .5em; 
-webkit-box-shadow: 0 1px 2px rgba(0,0,0,.2);
cursor:pointer;
display:inline-block;
font:14px/100% Arial,Helvetica,sans-serif;
margin:0 2px;
outline:medium none;
padding:0.5em 2em 0.55em;
text-align:center;
text-decoration:none;
text-shadow:0 1px 1px rgba(0, 0, 0, 0.3);
vertical-align:baseline;
}
#loading{
display:none;
}
p#info{
display:none;
background:none repeat scroll 0 0 #FFFFCC;
color:#222222;
padding:4px;
text-align:center;
}
</style>
<script type="text/javascript" language="javascript">
    function makeRequest(url) {
        var httpRequest;

        if (window.XMLHttpRequest) { // Mozilla, Safari, ...
            httpRequest = new XMLHttpRequest();
            if (httpRequest.overrideMimeType) {
                httpRequest.overrideMimeType('text/xml');
                // See note below about this line
            }
        } 
        else if (window.ActiveXObject) { // IE
            try {
                httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
            } 
            catch (e) {
                try {
                    httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                } 
                catch (e) {}
            }
        }

        if (!httpRequest) {
            alert('Giving up :( Cannot create an XMLHTTP instance');
            return false;
        }
        httpRequest.onreadystatechange = function() { handleResponse(httpRequest); };
        httpRequest.open('GET', url, true);
        httpRequest.send('');

    }

    function handleResponse(httpRequest) {

        if (httpRequest.readyState == 4) {
            if (httpRequest.status == 200) {
                renderTimetable(httpRequest.responseText);
            } else {
                alert('There was a problem with the request.');
            }
        }

    }

    function renderTimetable(responseText)
    {
        timeTables = eval( '(' + responseText + ')' );        

        var output = '';
        var zClass = '';
        if (!timeTables.length > 0) {
            document.getElementById('info').innerHTML = 'Sorry, No Info available for your selection';
            document.getElementById('info').style.display = 'block';
        }

        document.getElementById('loading').style.display = 'none';
        for(i = 0 ; i < timeTables.length; i++) {
            output += '<table class="timeTable">';
            for(j = 0; j < timeTables[i].length; j++) {
                output += '<thead>';
                output += '<tr>';
                output += '<th colspan="3">' + timeTables[i][j].train + '</th>';
                output += '</tr>';
                output += '</thead>';
                output += '<tbody>';
                if(zClass == 'odd') {
                    zClass = '';
                } else {
                    zClass = 'odd';
                }
                output += '<tr class="' + zClass + '">';
                output += '<td class="station"><span>' + timeTables[i][j].depStation + '</span></td>';
                output += '<td class="platform"><span>'+ timeTables[i][j].depPlatform + '</span></td>'; 
                output += '<td class="time"><span>' + timeTables[i][j].depTime + '</span></td>';
                output += '</tr>';
                if(zClass == 'odd') {
                    zClass = '';
                } else {
                    zClass = 'odd';
                }
                output += '<tr>';
                output += '<td class="station"><span>' + timeTables[i][j].arrStation + '</span></td>';
                output += '<td class="platform"><span>'+ timeTables[i][j].arrPlatform + '</span></td>'; 
                output += '<td class="time"><span>' + timeTables[i][j].arrTime + '</span></td>';
                output += '</tr>';
                output += '</tbody>';
            }
            output += '</table>';
        }
        document.getElementById('timeTable').innerHTML = output;
    }
    
    function getTimetable() {
        document.getElementById('timeTable').innerHTML = '';
        document.getElementById('info').style.display = 'none';
        document.getElementById('loading').style.display = 'block';
        makeRequest('getTimetable.php?from=' + document.getElementById('from').value + '&to=' + document.getElementById('to').value);
        return false;
    }
</script>
<meta name="viewport"
 content="width=device-width, initial-scale=1, maximum-scale=1"/>
</head>
<body>
<div id="wrapper">
<form onsubmit="getTimetable();return false;">
    <p>
    <label for="from">From</label>
    <select name="from" id="from">
    <?php
    foreach ($stations as $station):
       echo '<option value="' . $station . '">' . $station . '</option>';
    endforeach
    ?>
    </select>
    </p>
    <p>
    <label for="To">To</label>
    <select name="to" id="to">
    <?php
    foreach ($stations as $station):
       echo '<option value="' . $station . '">' . $station . '</option>';
    endforeach
    ?>
    </select>
    </p>
    <p>
        <input type="submit" class="button white"/>
        <span id="loading"><img src="loading.gif" />Loading..</span>
    </p>
</form>
<p id="info"></p>
    <div id="timeTable">
    </div>
</div>
</body>

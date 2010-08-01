<?php
require dirname(__FILE__) . '/stations.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
<head>
<title>Mobile Timetable</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
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
 a {
	color:#523A0B;
	text-decoration:none;
	}
 a:visited {
	color:#444;
	font-weight:normal;
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
#navcontainer ul
{
    padding-left: 0;
    margin-left: 0;
    background: none repeat scroll 0 0 #EBE5D9;
color: White;
float: left;
width: 100%;
       font-family: arial, helvetica, sans-serif;
}

#navcontainer ul li { display: inline; }

#navcontainer ul li a
{
padding: 0.2em 1em;

    background: none repeat scroll 0 0 #EBE5D9;
color: #000;
       text-decoration: none;
float: left;
       border-right: 1px solid #fff;
       border-bottom-width:0;
}

#navcontainer ul li a:hover, #navcontainer ul li a.current
{
background-color:#ffffee;
color: #000;
}
#home,#about,#history {
clear:both;
}
#about,#history {
display:none;
}
.odd {
	border-color:#EBE5D9;
	background:#F7F4EE;
}
ul#history_list {
    list-style-type: none;
    padding-left: 0;
    margin-left: 0;
}
ul#history_list li{
   list-style: none;
   padding: 0.8em .5em;
}
ul#history_list li:hover {
	background:#ffffee;
	border-color:#523A0B;
}
ul#history_list li a{
    border-bottom: 1px dotted;
}
img#my_photo{
float:left;
margin-right:7px;
width:150px;
}
div#about_text{
    vertical-align:top;
}
div#about_text ol {
    margin-left: 139px;
}

</style>
<script type="text/javascript" language="javascript">
    sections = ["home", "history", "about"];
    navlists = ["1_1", "2_1", "3_1"];
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
        document.getElementById('loading').style.display = 'none';
        if (!timeTables.length > 0) {
            document.getElementById('info').innerHTML = 'Sorry, No Info available for your selection';
            document.getElementById('info').style.display = 'block';
            return;
        }
        for(i = 0 ; i < timeTables.length; i++) {
            if(!timeTables[i].length > 0) {
                continue;
            }
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
        var key = document.getElementById('from').value + '_-_' + document.getElementById('to').value;

        if(!localStorage['history']) {
            var init = {};
            init.count = 0;
            init.queries = {};
            localStorage['history'] = JSON.stringify(init); 
        }
        var historys = JSON.parse(localStorage['history']);
        var d = new Date();
        if(!historys.queries[key]) {
            var count = historys.count + 1;
            historys.count = count;
            historys.queries[key] = {accessCount: 1, lastAccess: d.getTime()};
            localStorage['history'] = JSON.stringify(historys);
        } else {
            var accessCount = historys.queries[key].accessCount + 1;
            historys.queries[key] = {accessCount: accessCount, lastAccess: d.getTime()};
            localStorage['history'] = JSON.stringify(historys);
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
    function showSection(id) {
        document.getElementById('info').style.display = 'none';
        var section = document.getElementById(id);
        if(!section) {
            return;
        }
        if(-1 == sections.indexOf(id)) {
            return;
        }
        for(s in sections) {
            if(id === sections[s]) document.getElementById(sections[s]).style.display = 'block';
            else document.getElementById(sections[s]).style.display = 'none';
        }
    }
    function selectNav(id) {
        var nav = document.getElementById(id);
        if(!nav) {
            return;
        }
        if(-1 == navlists.indexOf(id)) {
            return;
        }
        for(n in navlists) {
            if(id === navlists[n]) document.getElementById(navlists[n]).setAttribute((document.all ? 'className' : 'class'), "current");
            else document.getElementById(navlists[n]).removeAttribute(document.all ? 'className' : 'class');
        }
    }
    function showHistory() {
        document.getElementById('history').innerHTML = '';
        if(!localStorage['history']) {
            document.getElementById('info').innerHTML = 'History Cleared/Nothing in History, yet!';
            document.getElementById('info').style.display = 'block';
            return;
        }
        var historys = JSON.parse(localStorage['history']);
        if(!historys.count > 0) {
            document.getElementById('info').innerHTML = 'History Cleared/Nothing in History, yet!';
            document.getElementById('info').style.display = 'block';
            return;
        }
        var liClass = '';
        var output = '<ul id="history_list">';
        var hSortable = [];
        for(h in historys.queries) {
            hSortable.push({route : h, accessCount: historys.queries[h].accessCount, lastAccess: historys.queries[h].lastAccess});
        }
        hSortable.sort(sortByAccessTime);
        for(h in hSortable) {
            liClass = (liClass == '') ? 'odd' : '';
            var stations = hSortable[h].route.split('_-_');
            output += '<li class="' + liClass + '">';
            output += '<a href="?from=' + stations[0] + '&to=' + stations[1] + '">' + stations[0] + ' to '+ stations[1] + '</a>';
            output += '</li>';
        }
        output += '</ul>';
        output += '<a href="#" onclick="localStorage.clear(\'history\');showHistory();return false;">Clear all History</a>';
        document.getElementById('history').innerHTML = output;
    }
    
    function sortByAccessTime(a,b) {
        return  -1 * (a.lastAccess - b.lastAccess);
    }

    function sortByAccessCount(a,b) {
        return  -1 * (a.accessCount - b.accessCount);
    }
</script>
</head>
<body>
    <div id="wrapper">
        <div id="navcontainer">
            <ul id="navlist">
                <li id="active"><a href="#" onclick="selectNav('1_1');showSection('home');return false;" id="1_1" class="current">Home</a></li>
                <li><a href="#" onclick="selectNav('2_1');showSection('history');showHistory();return false;" id="2_1">History</a></li>
                <li><a href="#" onclick="selectNav('3_1');showSection('about');return false" id="3_1">About</a></li>
            </ul>
        </div><!-- div#navcontainer -->
        <div id="home">
        <form onsubmit="getTimetable();return false;">
            <p>
                <label for="from">From</label>
                <select name="from" id="from">
                <?php
                    foreach ($stations as $station):
                       echo '<option value="' . $station . '"';
                       if($_GET['from'] === $station):
                           echo ' selected="selected" ';
                       endif;
                       echo '>' . $station . '</option>';
                    endforeach
                ?>
                </select><!-- select#from -->
            </p>
            <p>
                <label for="To">To</label>
                <select name="to" id="to">
                <?php
                foreach ($stations as $station):
                    echo '<option value="' . $station . '"';
                    if($_GET['to'] === $station):
                        echo ' selected="selected" ';
                    endif;
                    echo '>' . $station . '</option>';
                endforeach
                ?>
                </select><!-- select#to -->
            </p>
            <p>
                <input type="submit" class="button white"/>
                <span id="loading"><img src="loading.gif" />Loading..</span>
            </p>
        </form>
        <div id="timeTable"></div>
    </div><!-- div#home -->
    <div id="history"></div>
    <div id="about">
        <img src="k7.jpg" id="my_photo" alt="k7" />
        <div id="about_text">
        <span>I created this page for 2 reasons:</span>
            <ol>
                <li>I wasn't too happy with the cityrail apps available for Android phones.</li>
                <li>I wanted to play around with some HTML5 features.</li>
            </ol>
            <h5>Privacy</h5>
            Your search History is stored in your Device and you can clear it by clicking "Clear all History" at the bottom of the History page or by using your Browsers/Devices normal mechanism.
            <h5>About Me</h5>
            My name is Kesavan(k7). I am a web developer working in Sydney. You can contact me on k7@trk7.com.
        </div><!-- div#about_text -->
    </div><!-- div#about -->
    <p id="info"></p>
</div><!-- wrapper -->
<?php
    if(!empty($_GET['from']) && !empty($_GET['to'])) :
        if(in_array($_GET['from'], $stations) && in_array($_GET['to'], $stations)) :
?>
    <script type="text/javascript">
        getTimetable();
    </script>
<?php
        endif;
    endif;
?>
</body>
</html>

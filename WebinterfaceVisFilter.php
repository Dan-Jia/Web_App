<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Visualization</title>
  <link href="style.css" rel="stylesheet" type="text/css" href="css/style.css.php">

  <link rel="stylesheet" href="./lib/leaflet.css" />
  <style>
    .map {
      display: inline-block;
      height: 400px; /*800*/
      width:  1000px; /*1000*/
      margin: 0;
      padding: 0;
    }
    html, body {
      margin: 10;
      padding: 0;
    }
  </style>

<script src="./libGraph/raphael-min.js"></script>
<script src="./libGraph/g.raphael-min.js"></script>
<script src="./libGraph/g.line-min.js"></script>
<script src="./libGraph/g.raphael.js"></script>
<script src="./libGraph/g.bar.js"></script>
<script>

function drawPolygons() {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      polyList = JSON.parse(xmlhttp.responseText);
      var i;
      for(i=0;i<polyList.length;i++){
       var polygon=polyList[i];
       var wktReader = new Wkt.Wkt( polygon );
       var feature = wktReader.toObject();
       feature.addTo( map );
      }//for
    }//if
  };//function
  xmlhttp.open("GET","queryPolygonsForMap.php?"+
                "&sensor0="+document.getElementById('sensor0').value+
                "&starttime="+document.getElementById('starttime').value+
                "&stoptime="+document.getElementById('stoptime').value,
                true);
  xmlhttp.send();
}//function

function drawLinechart() {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      var retData = JSON.parse(xmlhttp.responseText);
      var x=[];
      var y=[];
      var xAxis=[];
      var offset=retData[0].key;
      var maxYVal=0;
      for(var i=0;i<retData.length;i++){
        x[i]=retData[i].key;
        x[i]=(x[i]-offset)/86400000;//millisecs. per day
        y[i]=retData[i].doc_count;
        maxYVal=Math.max(maxYVal,y[i]);
        xAxis[i]=retData[i].key_as_string;
      }
      var yAxisSteps=Math.min(maxYVal,5);
      yAxisSteps=Math.round(yAxisSteps);
      document.getElementById("chartContainer").innerHTML =[];
      var paper = new Raphael(document.getElementById("chartContainer"),1000,450);
      var chart = paper.linechart(30, 30, 900, 400, x, y, {
       axis: "0 0 1 1",axisystep: yAxisSteps
     });

     paper.text(100,20, "Number of measurements").attr({"font-size":"13px","fill":"black"});

     var ticks=Math.round(chart.axis[0].text.items.length/8);
      for(var i=0;i<chart.axis[0].text.items.length;++i){
        if(i%ticks==0){
          var index=Math.round((x.length/chart.axis[0].text.items.length)*i);
          chart.axis[0].text.items[i].attr({'text':xAxis[index]});
        }else{
          chart.axis[0].text.items[i].attr({'text':' '});
        }
      }//for
    }//if
  };//function
  xmlhttp.open("GET",
    "queryDatesForLineChart.php?"+
                "&sensor0="+document.getElementById('sensor0').value+
                "&region="+document.getElementById('region').value+
                "&starttime="+document.getElementById('starttime').value+
                "&stoptime="+document.getElementById('stoptime').value,
                true);
  xmlhttp.send();
}//function

function drawBarchart() {
 var xmlhttp = new XMLHttpRequest();
 xmlhttp.onreadystatechange = function() {
   if (this.readyState == 4 && this.status == 200) {
     var retData = JSON.parse(xmlhttp.responseText);
     // document.getElementById("table").innerHTML=xmlhttp.responseText;
     var labelSensors=[];
     var y=[];
     for(var i=0;i<retData.length;i++){
       labelSensors[i]=retData[i].key;
       y[i]=retData[i].doc_count;
     }//for
     txtattr = {
       font: "11px 'Arial', Arial, sans-serif"
     };
     var breite = 80*retData.length;
     document.getElementById("barContainer").innerHTML =[];
     var paper = new Raphael(document.getElementById("barContainer"),1000,350);
     var barChart = paper.barchart(0, 35, breite , 300, y).attr({fill: "#5097a4"});
     var textAbstand = (breite-20)/(retData.length);
     for (var i = 0;i<retData.length;i++) {
       paper.text(i*textAbstand+45, 330, labelSensors[i]).attr(txtattr);
       paper.text(i*textAbstand+45, 305, y[i]).attr(txtattr);
     }//for
   }//if
 };//function
 xmlhttp.open("GET",
   "queryFrequencysForBarChart.php?"+
               "&starttime="+document.getElementById('starttime').value+
               "&stoptime="+document.getElementById('stoptime').value,
               true);
 xmlhttp.send();
}//function

</script>
</head>

<body>
  <?php
  include_once 'config.php';
  include 'createFilterList.php';
  $sensorList=createFilterList('sensor0');
  $missionList=createFilterList('mission0');
  $region0List=createFilterList('region0');
  $region1List=createFilterList('region1');
  $regionList=array_merge($region0List, $region1List);
  ?>
  <header>
  <duv id="Titel">Visualization Project Vis-1</div>
  <div id="subtitel">Computational and Data Science - SS2018</div>
  </header>

  <section>
    <nav>
      <ul>
        <h2>Navigation</h2><br>
        <li><a href="WebinterfaceDataMgt.php">Upload data files</a></li>
        <hr>
        <li><a href="gridDataUploadToDatabase.php">Start uploading grid data</a></li>
        <hr>
        <li><a href="WebinterfaceVisFilter.php">Query data from uploaded files</a></li>
      </ul>
    </nav>
    <article id="main">

  <!-- Balkengraphik: ///////////////////////////////// -->
  <h3>Sensors in use within specified time interval:</h3>
  <p id="main">1. Select a time interval that the following graphs depend on.</p>

  Start time:<input type="text" id="starttime" placeholder="YYYY-MM-DD">
  Stop time:<input type="text" id="stoptime" placeholder="YYYY-MM-DD">
  <input type="button" name="query" value="Query" onclick="drawBarchart()"/><br>

  <a id="barContainer"></a><br/><br/>

  <a href="downloadBarChartCSV.php"><button>Download sensor frequency</button></a><br><br>


<!-- Karte: ///////////////////////////////// -->
  <br/><h3>Geographical coverage by specified sensor:</h3>
  <p id="main">2. Visualizing the polygons of the taken images requires uploading grid data (on the navigaton bar). This will take a few minutes. </p>
  <p id="main">3. Select a sensor within the given time interval. </p>

  <form>
  <select name='sensor0' id='sensor0' >
    <option value="">Select sensor:</option>
    <?php for ($i=0;$i<sizeof($sensorList);$i++) {
      ?>
    <option><?php echo($sensorList[$i]); ?></option>
    <?php
  } ?>
  </select>
  <input type="button" name="query" value="Query" onclick="drawPolygons()"/>
  </form><br>

  <div id="map001" class="map"></div>
  <script src="./lib/leaflet.js"></script>
  <script src="./lib/wicket.js"></script>
  <script src="./lib/wicket-leaflet.js"></script>
  <script>
    // create map
    const map = L.map( 'map001' );
    // set center point and zoom
    map.setView( [ 59.42, 20.57 ], 4 );
    // chose a layer
    const osmLayer = L.tileLayer(
      'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 10,
        attribution: '&copy; OpenStreetMap',
    });
    osmLayer.addTo( map );
  </script>

  <br/><br/>
  <a href="downloadPolyCSV.php">
    <button>Download polygons</button>
  </a><br/><br/>


<!-- Kurve: ///////////////////////////////// -->

<br><h3>Sensor measurements within specified study region:</h3>
<p id="main">4. Select a region where the given sensor was taking imageries during the given time interval. </p>

  <form>
  <select name='region' id='region' >
    <option value="">Select region:</option>
    <?php for ($i=0;$i<sizeof($regionList);$i++) {
      ?>
    <option value=<?php print_r($regionList[$i]); ?> > <?php echo($regionList[$i]); ?></option>
    <?php
  } ?>
  </select>

  <input type="button" name="query_2" value="Query" onclick="drawLinechart()"/>
  </form>

  <a id="chartContainer"></a><br/><br/>

  <a href="downloadLineChartCSV.php"><button>Download time series</button></a>
  <br/><br/>

</article>
</section>

</body>
</html>

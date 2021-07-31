<?php

$p_cnt = 0;     //Nombre de marqueurs
$jmarqueur="";  //A peupler pour javascript
$contenu="";

// Si nous arrivons du formulaire
if (isset($_POST["v"])){
	$quelfic = stripSlashes($_POST["p"]);
	$fic_complement = str_replace(".jpg",".xml",$quelfic);
	$xml="<?xml version=\"1.0\" standalone=\"yes\"?>\n";
	$xml.="<pano>\n";
	$xml.="<titre>";
	$xml.=rtrim($_POST['titre']);
	$xml.="</titre>\n";
	$xml.="<legende>\n";
	$xml.=rtrim($_POST['legende']);
  $xml.="</legende>\n";
  $xml.=$_POST['contenu'];
	$xml.="</pano>\n";
	file_put_contents($fic_complement, $xml);
	$msg = "Enregistrement effectué";
	//echo $fic_complement;
}	

if (!isset($quelfic)) $quelfic = stripSlashes($_GET["p"]);	

// On recupere les elements eventuel pour les marqueur
$fic_complement = str_replace(".jpg",".xml",$quelfic);
if (file_exists($fic_complement)){
	$xml = simplexml_load_file($fic_complement);
	$titre=$xml->titre;
  $legende=$xml->legende;
  // Calcul nombre de marqueur
  $p_cnt = count($xml->marker);
  // On construit le tableau javascript des marqueurs
  for($i = 0; $i < $p_cnt; $i++) {
    $jmarqueur.="a.push({\n";
    $jmarqueur.="\t id       : 'Marker".$i."',\n";
    $jmarqueur.="\t tooltip  : {\n";
    $jmarqueur.="\t\t content : '".addslashes($xml->marker[$i]->titre)."',\n";
    $jmarqueur.="\t\t position: 'bottom right',\n";
    $jmarqueur.="\t },\n";
    $jmarqueur.="\t content  : document.getElementById('pin-".$i."').innerHTML,\n";
    $jmarqueur.="\t latitude : ".$xml->marker[$i]->latitude.",\n";
    $jmarqueur.="\t longitude: ".$xml->marker[$i]->longitude.",\n";
    $jmarqueur.="\t image    : 'example/assets/pin-".$xml->marker[$i]->couleur.".png',\n";
    $jmarqueur.="\t width    : 32,\n";
    $jmarqueur.="\t height   : 32,\n";
    $jmarqueur.="\t anchor   : 'bottom center',\n";
    $jmarqueur.="});\n";
    $contenu.="<marker>\n";
		$contenu.="<titre>".$xml->marker[$i]->titre."</titre>\n";
		$contenu.="<couleur>".$xml->marker[$i]->couleur."</couleur>\n";
		$contenu.="<latitude>".$xml->marker[$i]->latitude."</latitude>\n";
		$contenu.="<longitude>".$xml->marker[$i]->longitude."</longitude>\n";
		$contenu.="<descmarqueur>".$xml->marker[$i]->descmarqueur."</descmarqueur>\n";
		$contenu.="</marker>\n";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $titre; ?></title>

  <link rel="stylesheet" href="dist/photo-sphere-viewer.css">
  <link rel="stylesheet" href="dist/plugins/markers.css">

  <style>
    html, body {
      width: 100%;
      height: 100%;
      /*overflow: hidden; */
      margin: 0;
      padding: 0;
    }

    #photosphere {
      width: 60%;
      height: 60%;
    }

    .psv-button.custom-button {
      font-size: 22px;
      line-height: 20px;
    }

    .demo-label {
      color: white;
      font-size: 20px;
      font-family: Helvetica, sans-serif;
      text-align: center;
      padding: 5px;
      border: 1px solid white;
      background: rgba(0, 0, 0, 0.4);
    }
  </style>
</head>
<body>
 <div style="float:right;padding-left:10px;padding-right:10px;">

    <form action="pano-xml.php" method="post" class="form-example"> 
    <input type="submit" name="submit" id="submit" value="Valider" /> <a href="index.php?a=admin">Retour</a><br />
    <input id="p" name="p" type="hidden" value="<?php echo $quelfic; ?>">
    <input id="v" name="v" type="hidden" value="ok">
    <hr />
    <label for="name">Latitude, longitude (pour copier coller): </label><br />
    <input type="text" name="latitudetmp" id="latitudetmp" size="50"><br />
    <hr />
    <label for="titre">Titre (Pour la liste des sphères</label><br />
    <input type="text" name="titre" id="titre" size="50" maxlength="250" value="<?php echo $titre; ?>" /><br />  
    <label for="id_Legende">Legende (Pour la liste des sphères):</label><br />
    <textarea name="legende" id="legende" rows="5" cols="50" wrap="soft"><?php echo $legende; ?></textarea><br />
    <hr />
    <h2>XML Marqueurs</h2>
    <textarea name="contenu" id="contenu" rows="10" cols="50" wrap="soft"><?php echo $contenu; ?></textarea><br />
<xmp>
<marker>
<titre>...</titre>
<couleur>red ou blue</couleur>
<latitude>...</latitude>
<longitude>...</longitude>
<descmarqueur>...</descmarqueur>
</marker>
</xmp>
    <input type="submit" name="submit" id="submit" value="Valider" />
   </form>
  
</div> 
<div id="photosphere"></div>
<script src="node_modules/three/build/three.js"></script>
<script src="node_modules/promise-polyfill/dist/polyfill.js"></script>
<script src="node_modules/uevent/browser.js"></script>
<script src="node_modules/nosleep.js/dist/NoSleep.js"></script>
<script src="dist/photo-sphere-viewer.js"></script>
<script src="dist/plugins/gyroscope.js"></script>
<script src="dist/plugins/stereo.js"></script>
<script src="dist/plugins/markers.js"></script>

<!-- text used for the marker description -->
<?php
for($i = 0; $i < $p_cnt; $i++) {
  echo "<script type=\"text/template\" id=\"pin-".$i."\">\n";
  echo $xml->marker[$i]->descmarqueur."\n";
  echo "</script>\n";
}
?>

<script>
  const PSV = new PhotoSphereViewer.Viewer({
    container : 'photosphere',
    //panorama  : '../Panos/Lorient-pano.jpg',
    //caption   : 'Parc national du Mercantour <b>&copy; Damien Sorel</b>',
    panorama   : '<?php echo $quelfic; ?>',
    caption    : '<?php echo $queltit; ?>',
    loadingImg: 'example/assets/photosphere-logo.gif',
    navbar    : [
      'autorotate', 'zoom', 'download', 'markers', 'markersList',
      {
        content  : '💬',
        title    : 'Show all tooltips',
        className: 'custom-button',
        onClick  : function () {
          markers.toggleAllTooltips();
        }
      },
      'caption', 'gyroscope', 'stereo', 'fullscreen',
    ],
    plugins   : [
      PhotoSphereViewer.GyroscopePlugin,
      PhotoSphereViewer.StereoPlugin,
      [PhotoSphereViewer.MarkersPlugin, {
        markers: (function () {
          var a = [];
          <?php echo $jmarqueur; ?> 
          return a;
        }())
      }]
    ]
  });

  var markers = PSV.getPlugin(PhotoSphereViewer.MarkersPlugin);

  PSV.on('click', function (e, data) {
    if (!data.rightclick) {
      markers.addMarker({
        id       : '#' + Math.random(),
        tooltip  : 'Generated marker',
        longitude: data.longitude,
        latitude : data.latitude,
        image    : 'example/assets/pin-red.png',
        width    : 32,
        height   : 32,
        anchor   : 'bottom center',
        data     : {
          deletable: true,
        },
      });
      console.log('latitude:',data.latitude,'longitude:',data.longitude);
      document.getElementById('latitudetmp').value = "<latitude>" + data.latitude + "</latitude><longitude>" + data.longitude + "</longitude>"
      //document.getElementById('latitudetmp').value = 10;
    }
  });

  markers.on('select-marker', function (e, marker, data) {
    console.log('select', marker.id);
    console.log('latitude:',data.latitude,'longitude:',data.longitude);
    if (marker.data && marker.data.deletable) {
      if (data.dblclick) {
        markers.removeMarker(marker);
      }
      else if (data.rightclick) {
        markers.updateMarker({
          id   : marker.id,
          image: 'example/assets/pin-blue.png',
        });
      }
    }
  });

  markers.on('unselect-marker', function (e, marker) {
    console.log('unselect', marker.id);
  });

  markers.on('over-marker', function (e, marker) {
    console.log('over', marker.id);
  });

  markers.on('leave-marker', function (e, marker) {
    console.log('leave', marker.id);
  });

  markers.on('select-marker-list', function (e, marker) {
    console.log('select-list', marker.id);
  });

  markers.on('goto-marker-done', function (e, marker) {
    console.log('goto-done', marker.id);
  });
</script>
</body>
</html>
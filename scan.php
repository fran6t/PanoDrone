<?php

$dir = "Spheres";

// Run the recursive function 

$response = scan($dir);


// This function scans the files folder recursively, and builds a large array

function scan($dir){

	$files = array();

	// Is there actually such a folder/file?

	if(file_exists($dir)){
	
		foreach(scandir($dir) as $f) {
		
			if(!$f || $f[0] == '.' || pathinfo($f, PATHINFO_EXTENSION )=="xml") {
				continue; // Ignore hidden files
			}

			if(is_dir($dir . '/' . $f)) {

				// The path is a folder

				$files[] = array(
					"name" => $f,
					"titre"=> "",
					"type" => "folder",
					"path" => $dir . '/' . $f,
					"items" => scan($dir . '/' . $f) // Recursively get the contents of the folder
				);
			}
			
			else {

				// It is a file
				// On cherche si un fichier .xml existe
				$titre="";
				$legende="";
				$pano_xml = $dir."/".str_replace(".jpg",".xml",$f);
				if (file_exists($pano_xml)){
					$xml = @simplexml_load_file($pano_xml);
					$titre=(string)$xml->titre;
					$legende=(string)$xml->legende;
				}
				if (rtrim($titre)=="") $titre = $f;
				$files[] = array(
					"name" => $f,
					"titre"=> $titre,
					"legende"=> $legende,
					"type" => "file",
					"path" => $dir . '/' . $f,
					"size" => filesize($dir . '/' . $f) // Gets the size of this file
				);
			}
		}
	
	}

	return $files;
}



// Output the directory listing as JSON

header('Content-type: application/json');

echo json_encode(array(
	"name" => $dir,
	"type" => "folder",
	"path" => $dir,
	"items" => $response
));

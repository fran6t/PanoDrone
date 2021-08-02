<?php

$dir = "Spheres";

$mabdd = "pano.db";
$db = new SQLite3($mabdd);

// Decommente les 5 lignes une fois pour remette a zero la bdd attention irreversible
// $db = new SQLite3($mabdd);
// $SqlString = "drop table if exists lespanos"; 
// $db->exec($SqlString);
// $SqlString = "drop table if exists lespanos_details"; 
// $db->exec($SqlString);



$SqlString = "CREATE TABLE IF NOT EXISTS lespanos
    ( fichier TEXT, titre TEXT, legende TEXT)"; 
$db->exec($SqlString);

$SqlString ="CREATE TABLE IF NOT EXISTS lespanos_details
    (fichier TEXT, nom_marqueur TEXT, couleur TEXT, latitude TEXT, longitude TEXT, descri TEXT)";
$db->exec($SqlString);

// Run the recursive function 

$response = scan($dir);


// This function scans the files folder recursively, and builds a large array

function scan($dir){
	global $db;
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
				// On recupere le titre et la legende sinon on insert
				$titre="";
				$legende="";
				$fichier = $dir . '/' . $f;
				$statement = $db->prepare('SELECT titre,legende FROM lespanos WHERE fichier = :fichier LIMIT 1;');
				$statement->bindValue(':fichier', $fichier);
				$result = $statement->execute();
				$row=$result->fetchArray(SQLITE3_ASSOC);
				// check for empty result
				if ($row != false) { // Trouvé
					$titre	= $row['titre'];
					$legende= $row['legende'];
				} else {
					// J'ai pas trouvé alors il faut insert
    				$statement = $db->prepare('INSERT INTO lespanos (fichier) VALUES (:fichier);');
					$statement->bindValue(':fichier', $fichier);
					$result = $statement->execute();
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

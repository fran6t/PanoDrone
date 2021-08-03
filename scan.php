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

$tableCheck =$db->query("SELECT name FROM sqlite_master WHERE name='lespanos'");
if ($tableCheck->fetchArray() === false){
    // La table existe pas creation
    $SqlString = "CREATE TABLE [lespanos] (
	                [fichier] VARCHAR(500)  NULL,
	                [titre] VARCHAR(500)  NULL,
	                [legende] TEXT  NULL,
	                [hashfic] VARCHAR(100)  NULL
	            );";
    $db->exec($SqlString);
    $SqlString = "CREATE INDEX [IDX_LESPANOS_fichier] ON [lespanos]([fichier]  ASC );";
    $db->exec($SqlString);
}

$tableCheck =$db->query("SELECT name FROM sqlite_master WHERE name='lespanos_details'");
if ($tableCheck->fetchArray() === false){
    $SqlString = "CREATE TABLE [lespanos_details] (
        [fichier] VARCHAR(500)  NULL,
        [hashfic] VARCHAR(100)  NULL,
        [nom_marqueur] VARCHAR(100)  NULL,
        [couleur] VARCHAR(10)  NULL,
        [latitude] VARCHAR(20)  NULL,
        [longitude] VARCHAR(20)  NULL,
        [descri] TEXT  NULL
        );";
    $db->exec($SqlString);
    $SqlString = "CREATE INDEX [IDX_LESPANOS_DETAILS_hashfic] ON [lespanos_details]([hashfic]  ASC);";
    $db->exec($SqlString);
}

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

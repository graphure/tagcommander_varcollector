<?php

//header utilisés pour permettre le cross domain
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
header('Access-Control-Allow-Methods: GET, POST');

//inclusion des classes php Excel
include 'PhpExcel/PHPExcel.php';
include 'PhpExcel/PHPExcel/Writer/Excel2007.php';


//inclusion de la classe de recuperation de variables
require 'class/collectedvars.php';


//recuperation des éléments envoyés en post
 $json = file_get_contents('php://input');

 // encodage en utf8 (eviter conflis de caracteres)
 $datalayer= utf8_encode($json);

//encodage en format json
$datalayer=json_decode($datalayer,true);



//préparation fichier excel
$objPHPExcel = new PHPExcel;
$sheet = $objPHPExcel->getActiveSheet();

// recuperation du template de base dans le fichier tmp
$objPHPExcel = PHPExcel_IOFactory::load("tmp/datalayer.xlsx");

//formatage general du fichier
$sheet = $objPHPExcel->getActiveSheet();
$sheet->getDefaultStyle()->getFont()->setName('Century Gothic')->setSize(9);


// creation d'un nouvel objet collectedvars
$myVars = new collectedVars;

// on commence dans le fichier excel a la ligne 6 et la colonne 7
$myVars->row=6;
$myVars->columnOrigin=7;

//preparation des colomnes (sans doute a optimiser)
$myVars->Alphabet=array("B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ");

//initialisation du compteur de lignes
$myVars->count=1;

//transfert du datalayer dans la classe
$myVars->datalayer=$datalayer;

//recuperation du fichier excel pour la classe
$myVars->sheet=$sheet;

//mise an place des formats
$myVars->title="D8D8D8";
$myVars->error="FF0000";
$myVars->empty="ABABAB";
$myVars->varTitle="848484";
$myVars->array="38435E";
$myVars->subarray="96B6FF";
$myVars->URL="F3E2A9";



//on met toutes les colonnes au format voulu :
//la colonne de toutes les variables récupérées (un tableau par variable) mais on se donne une marge de 50 pour les sauts de ligne
$nmbVars=count($datalayer)+50;
for($i=0;$i<$nmbVars;$i++){
    //formatage de la colomne 
    $sheet->getColumnDimension("B")->setWidth(40);
    // toutes les valeurs sont visibles et la hauteur des cellules s'adaptent via le wrapText
    $sheet->getStyle("b".$i)->getAlignment()->setWrapText(true);
}


// formatage des autres colonnes
// on recupere la colomne
for($i=$myVars->columnOrigin;$i<count($myVars->Alphabet);$i++){
    // et on lui donne une dimension de 20
    $sheet->getColumnDimension($myVars->Alphabet[$i])->setWidth(20);
    for($j=0;$j<$nmbVars;$j++){
        // toutes les valeurs sont visibles et la hauteur des cellules s'adaptent via le wrapText
        $sheet->getStyle($myVars->Alphabet[$i].$j)->getAlignment()->setWrapText(true);
    }
}


// on insère le urls
$myVars->setTitles();
// et le titre de l'url
$sheet->setCellValue("B".$myVars->row,"URL");


//ligne suivante
$myVars->row++;




//variables obligatoires et prédéfinies
$predefined = array(
    "Environnement"=>["/env_/","Description Environnement"],
    "Tree Structure"=>["/page_/","Description de l'arborescence"],
    "Users"=>["/user_/","Description sur les User"],
    "List Products"=>["/list_/","Description pour les list produits"],
    "Articles"=>["/article/", "Description pour les articles"],
    "Products"=>["/product_/", "description sur les produits"],
    "Search pages"=>["/search_/", "Description pour les Search page"], 
    "Order Products"=>["/order_/","Description pour les pages de confirmation et panier"]
    );

//placement des variables prédéfinies et de leur valeurs
$match='';

foreach($predefined as $key => $value){
//on pose la clef dans la colonne B et la valeur dans chaque tableau

    foreach($myVars->datalayer as $subkey => $subvalue){
        //condition pour eviter que des fonctions se glissent dans la liste (javascript oriente objet)
        if(!preg_match("/function/",$subkey)){
            // on recupere d'abord toutes les variables prédéfinies
            if(preg_match($value[0],$subkey)){
                // si on retrouve uen variable predefinie
                if($match!=$key){
                    //on passe a la ligne suivante le nom de la varaible
                    $myVars->row++;
                    $sheet->setCellValue("B".$myVars->row,$key);
        			$myVars->setCellColor("B",$myVars->row,$myVars->varTitle);
                    $sheet->setCellValue("C".$myVars->row,$value[1]);
                    $myVars->row++;
                }   
                $sheet->setCellValue("B".$myVars->row,$subkey);

                $myVars->getVars($subkey);
                
                $match=$key;
                unset($datalayer[$subkey]);
            }
        }

    }

}
$myVars->row++;


//placement des autres variables non identifiées
ksort($myVars->datalayer);
foreach($myVars->datalayer as $key => $value){
    if(!preg_match("/function/",$key) && $key!="" && !preg_match("/Launched_tags/",$key)){
        $myVars->getVars($key);  
    }

}

// placement du nom des tags chargés
 $myVars->row++;  
 $myVars->getVars("Launched_tags");  


//suppression des vieux fichiers de plus d'une heure
$dir = "./tmp";
$dh  = opendir($dir);
while (false !== ($filename = readdir($dh))) {
	if(preg_match('/datalayer_[1-9]+/', $filename)){
    $time = explode("_",$filename);
    if(time()-$time[1]>=3600){
    	unlink('./tmp/'.$filename);
    }
    }
}

//creation du nouveau fichier
$writer = new PHPExcel_Writer_Excel2007($objPHPExcel);
$records = './datalayer'.time().'.xlsx';
$writer->save($records);


//récupération du nom du fichier
echo $records;



?>
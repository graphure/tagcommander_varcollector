<?php

Class collectedVars {
    //declaration des variables
    public $error;
    public $empty;
    public $array;
    public $subarray;

    public $title;
    public $datalayer;
    public $sheet;
    public $row;
    public $columnOrigin;
    public $Alphabet;
    
    //fonction de concaténation des élements du tableau
    //$tc_array : array
    public function arrayToString($tc_array){
        $string="";
         if(is_string($tc_array)==true){
                $string='"'.$tc_array.'"';
                
            }
        else if(is_array($tc_array)){
            if(isset($tc_array[0]) && is_array($tc_array[0])){
                $string="[\n";

                 //$string=json_encode($tc_array[0]);
                $string=implode(",\n",$this->arrayToString($tc_array[$i]);

                $string.="\n]";
            }else{
                //$string=json_encode($tc_array)
                $string="{";
                foreach($tc_array as $i=>$j){
                    $string.=$i." : '".$this->arrayToString($j)."',";
                }
                $string=$string."}";
            }
        }
        return $string;
    }


    //fonction de gestion des sub-array (order_products ... )
    //$tc_array : array
    //$column : number (current column)
    public function setArrayValues($tc_array,$column){
        if(is_array($tc_array)){
            foreach($tc_array as $i => $j){
                //key
                $this->sheet->setCellValue("B".$this->row,"        ".$i);

                //key cell format
                $this->setCellColor("B",$this->row,$this->array);

                //value
                $varValue= $this->arrayToString($j);
                if($varValue=='""'){
                    $varValue="empty";
                    $this->setCellColor($this->Alphabet[$this->columnOrigin+$j],$this->row,$this->empty);
                }
                if($varValue=='NaN' ||  $varValue=='undefined'){
                    $this->setCellColor($this->Alphabet[$this->columnOrigin+$j].$this->row,$this->error);
                }else{
                  $this->setCellColor($this->Alphabet[$column],$this->row,$this->subarray);
                }


                $this->sheet->setCellValue($this->Alphabet[$column].$this->row,$varValue);
                $this->row++;
            }
        }
    }


    /// placement des vars d'après leur nom
    //$string : string
    public function getVars($string){
        // on insère les url
        $collumn=$this->columnOrigin;
        $this->sheet->setCellValue("B".$this->row,$string);
        $this->setCellColor("B",$this->row,$this->title);

        //on stocke la valeur actuelle de la ligne dans une autre variable
        $setArrayValues_rows = $this->row;
        for($i=0;$i<=count($this->datalayer[$string]);$i++){
            $j=$i;

            isset($this->datalayer[$string][$i]);

            if(isset($this->datalayer[$string][$i])){
                $element= $this->datalayer[$string][$i];


                if(isset($this->Alphabet[$this->columnOrigin+$j])){
                    //on vérifie si c'est un tableau et on en récupère le premier élément
                    if(is_array($element) && isset($element[0])){
                        //on réinitialise $row dans sa valeur avant le traitement des array
                        $this->row=$setArrayValues_rows;
                        $this->row++;
                        $this->setArrayValues($element[0],$this->columnOrigin+$j);
                        $this->sheet->setCellValue("B".$this->row,$string." => Array");

                    }
                    $varValue= $this->arrayToString($element);
                    if($varValue=='""'){
                        $varValue="empty";
                        $this->setCellColor($this->Alphabet[$this->columnOrigin+$j],$this->row,$this->empty);
                    }
                    
                    $this->sheet->setCellValue($this->Alphabet[$this->columnOrigin+$j].$this->row,$varValue);
                }
            }
        }
        $this->row++;
        unset($this->datalayer[$string]);
    }


    // placement des urls
    public function setTitles(){
        $this->sheet->getRowDimension($this->row)->setRowHeight(150);
        //couleur de la cellule du titre titre
        foreach($this->datalayer["QA_category_name"] as $i=>$j){
            $styleArray = array(
                'borders' => array(
                    'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('argb' => '000000'),
                    ),
                ),
            );
            //poiser le style et couleur des titres de pages et url
            $this->setCellColor($this->Alphabet[$this->columnOrigin+$i],$this->row, $this->URL);
            $this->sheet->getStyle($this->Alphabet[$this->columnOrigin+$i].$this->row)->applyFromArray($styleArray);


            if(isset($j)){
                $this->sheet->getStyle($this->Alphabet[$this->columnOrigin+$i].$this->row)->getAlignment()->setTextRotation(45);
                $this->sheet->setCellValue($this->Alphabet[$this->columnOrigin+$i].$this->row,$j."\n".$this->datalayer["navigation"][$i]);
            }
        }
        unset($this->datalayer["QA_category_name"]);
        unset($this->datalayer["navigation"]);

    }

    // gestion de la couleur des fond
    public function setCellColor($column, $row, $color){
        $this->sheet->getStyle($column.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($color);
    }
}

?>
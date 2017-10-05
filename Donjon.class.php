<?php

require_once("LabyrDefault.class.php");

Class Donjon extends LabyrDefault{
    private $_iId;
    private $_sNomDonjon;

    /**
    * @param int $id l'id du donjon
    */
    public function __construct($id){
        $sql = "SELECT * FROM  donjon WHERE id_donjon = ".$id;

        $oConnexion = self::getConnexion();

        $oResult = $oConnexion->query($sql);
        $aResult = $oResult->fetch();
        if(!empty($aResult)){
            $this->_iIdDonjon  = $aResult["id_donjon"];
            $this->_sNomDonjon = $aResult["nom_donjon"];   
        }             
    }



    public static function All(){
        $oConnexion = self::getConnexion();
        $sListeDonjons = "SELECT id_donjon, nom_donjon FROM donjon ORDER BY nom_donjon";
        $oResult = $oConnexion->query($sDonjon);

        $aDonjon = array();
        foreach($oResult as $aRow){
            $aDonjon[] = new Donjon($aRow['id_donjon']);
        }
        return $aDonjon;
    }

    /**
    * @return string nom du donjon
    */
    public function getNomDonjon(){
        return $this->_sNomDonjon;
    }

    /**
    * @return int id du donjon
    */
    public function getIdDonjon(){
        return $this->_iIdDonjon;
    }

    /**
    * @param string nom du donjon
    */
    public function set_sNomDonjon($nom_donjon){
        $oConnexion = self::getConnexion();
        $sDonjon = "UPDATE donjon SET nom_donjon = '".$nom_donjon."' WHERE id_donjon = ".$this->_iIdDonjon;
        $oConnexion->exec($sDonjon);
        $this->_sNomDonjon = $nom_donjon;
    }
}
<?php

require_once("LabyrDefault.class.php");

Class Monstre extends LabyrDefault{
    private $_iIdMonstre;
    private $_sNomMonstre;
    private $_iPdvMonstre;

    public function __construct($id){
        $sql = "SELECT * FROM monstre WHERE id_monstre = ?";
        
        $oConnexion = self::getConnexion();
        
        $prepare = $oConnexion->prepare($sql);
        $oResult = $oConnexion->execute($prepare, $id);
        $aResult = $oResult->fetch();
        if(!empty($aResult)){
            $this->_iIdMonstre  = $aResult["id_monstre"];
            $this->_sNomMonstre = $aResult["nom_monstre"];
            $this->_iPdvMonstre = $aResult["pdv_monstre"]; 
        }
    }

    /**
    * @return string nom du monstre
    */
    public function getNom(){
        return $this->_sNomMonstre;
    }

    /**
    * @return int id du monstre
    */
    public function getId(){
        return $this->_iIdMonstre;
    }

        /**
    * @return int pdv du monstre
    */
    public function getPdv(){
        return $this->_iPdvMonstre;
    }

    /**
    * @param string nom du monstre
    */
    public function setNom($nom){
        $oConnexion = self::getConnexion();
        $sql = "UPDATE monstre SET nom_monstre = '".$nom."' WHERE id_monstre = ".$this->_iIdMonstre;
        $oConnexion->exec($sql);
        $this->_sNomMonstre = $nom;
    }

    /**
    * @param int pdv du monstre
    */
    public function setPdv($pdv){
        $oConnexion = self::getConnexion();
        $sql = "UPDATE monstre SET pdv_monstre = '".$pdv."' WHERE id_monstre = ".$this->_iIdMonstre;
        $oConnexion->exec($sql);
        $this->_sPdvMonstre = $pdv;
    }
}
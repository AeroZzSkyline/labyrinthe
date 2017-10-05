<?php

require_once("LabyrDefault.class.php");

Class Personnage extends LabyrDefault{
    private $_iId;
    private $_sNom;
    private $_iPdv;

    /**
    * @param int $id l'id du du personnage
    */
    public function __construct($id){
        $sql = "SELECT * FROM  personnage WHERE id_perso = ".$id;

        $oConnexion = self::getConnexion();

        $oResult = $oConnexion->query($sql);
        $aResult = $oResult->fetch();
        if(!empty($aResult)){
            $this->_iId  = $aResult["id_perso"];
            $this->_sNom = $aResult["nom_perso"];
            $this->_iPdv = $aResult["pdv_perso"]; 
        }             
    }

    public static function CreerPerso($nom,$pdv){
        $sql = "INSERT INTO personnage(nom_perso, pdv_perso) VALUES('".$nom."', ".$pdv.")";

        $oConnexion = self::getConnexion();

        $Result = $oConnexion->exec($sql);

        //renvoie l'id du perso créee
        $lastId = $oConnexion->lastInsertId(); 

        return new Personnage($lastId);
    }

    /**
    * @param $IdDonjon l'id du donjon
    * @return $iIdPieceEntree l'id de la piece d'entré du donjon
    */
    public function PlacerEntree($IdDonjon){
        //on recupère la pièce d'entrée du donjon passé en paramètre
        $SqlGetEntree = "SELECT id_piece, entree_piece, sortie_piece, id_monstre_piece, id_coffre_piece, id_donjon_piece
        FROM piece
        WHERE entree_piece = ? AND id_donjon_piece = ? ";
        $oConnexion = self::getConnexion();
        $prepare = $oConnexion->prepare($SqlGetEntree);
        $oResult = $oConnexion->execute($prepare, array(true, $IdDonjon));
        $aResult = $oResult->fetch();
        $iIdPieceEntree = $aResult['id_piece'];

        //verifier si il est deja dans la pièce
        $SqlDejaPlaceEntre = "SELECT Count(*) 
        FROM parcours 
        WHERE id_piece_parcours = ? AND id_perso_parcours = ? ";
        $prepare = $oConnexion->prepare($SqlDejaPlaceEntre);
        $oResult2 = $oConnexion->execute($prepare, array($iIdPieceEntree, $this->_iId));
        $aResult2 = $oResult->fetch();

        //si il y est déjà on fais rien
        if($aResult2 > 0){
            return false;
        }
        //sinon on le met dans la pièce de depart(on crée le parcours)
        else{
            $SqlInsertLigneParcours = "INSERT INTO parcours(id_perso_parcours, id_piece_parcours, date_visite_parcours) VALUES(? , ? , NOW())";
            $prepare = $oConnexion->prepare($SqlInsertLigneParcours);
            $oResult = $oConnexion->execute($prepare, array($this->_iId, $iIdPieceEntree));

            return $iIdPieceEntree;
        }     
    }


    public function PiecesPossible($IdPiece){
        //on recupère les sorties de la pièce passée en paramètre
        $sqlPiecesPossibles = "SELECT id_piece_sortie 
        FROM plan 
        WHERE id_piece_entree = ?";
        $oConnexion = self::getConnexion();
        $prepare = $oConnexion->prepare($sqlPiecesPossible);
        $oResult = $oConnexion->execute($prepare,array($IdPiece));
        $aResult = $oResult->fetchAll();

        //on renvoie un array des sorties possible
        return $aResult;
    }

    public function VerifPieceActuelle($IdPerso){
        //on récupère la dernière piece du du perso selon la date (table parcours)
        $sqlDernierePiece = "SELECT id_piece_parcours 
        FROM parcours 
        WHERE id_perso_parcours = ? 
        ORDER BY date_visite_parcours DESC
        LIMIT 1";
        $oConnexion = self::getConnexion();
        $prepare = $oConnexion->prepare($sqlDernierePiece);
        $oResult = $oConnexion->execute($prepare,array($IdPerso));
        $aResult = $oResult->fetch();

        return $aResult;
    }
    
    public function AvancerPiece($IdPiece){
        $sqlAvancerPiece = "INSERT INTO parcours(id_perso_parcours, id_piece_parcours, date_visite_parcours) VALUES(? , ? , NOW())";
        $oConnexion = self::getConnexion();
        $prepare = $oConnexion->prepare($sqlAvancerPiece);
        $oResult = $oConnexion->execute($prepare,array($this->_iId, $IdPiece));
    }

    public function VerifAvancer($IdPieceActuelle, $iIdPieceFuture){
        $sqlVerifAvancer = "SELECT Count(*)
        FROM plan 
        WHERE id_piece_entree = ? AND id_piece_sortie = ?";
        $oConnexion = self::getConnexion();
        $prepare = $oConnexion->prepare($sqlVerifAvancer);
        $oResult = $oConnexion->execute($prepare,array($IdPieceActuelle, $iIdPieceFuture));
        $aResult = $oResult->fetch();

        if(count($aResult) > 0){
            return true;
        }
        else{
            return false;
        }
    }

    public function CombattreMonstre($IdMonstre){
        //On recupère le nombre de trésor du joueur
        $sqlNombreTresor = "SELECT Count(*) FROM parcours WHERE id_perso_parcours = ?";
        //Pourcentage de base 40%
        $iPourcentage = 40;
        //Pour chaque trésor on augente de 5%
        for($i = 1; $i <= $iNombreTresor; $i++){
            $iPourcentage=+ 5;
        }
        if($iPourcentage > 80){
            $iPourcentage = 80;
        }

        //On tire un nombre entre 0 et 100
        $irand = rand(0,100);
        //Perdu -> On perd les pdv du monstre
        if($iRand > $iPourcentage){
        
        }else//Gagne -> on met monstre battu  en BDD
        {
            $sqlMonstreBattu = "UPDATE parcours SET monstre_tue_parcours = 1 WHERE "
        }
    }

    /**
    * @return string nom du perso
    */
    public function getNom(){
        return $this->_sNom;
    }

    /**
    * @return int id du perso
    */
    public function getId(){
        return $this->_iId;
    }

        /**
    * @return int pdv du perso
    */
    public function getPdv(){
        return $this->_iPdv;
    }

    /**
    * @param string nom du perso
    */
    public function setNom($nom){
        $oConnexion = self::getConnexion();
        $sql = "UPDATE personnage SET nom_perso = '".$nom."' WHERE id_perso = ".$this->_iId;
        $oConnexion->exec($sql);
        $this->_sNom = $nom;
    }

    /**
    * @param int pdv du perso
    */
    public function setPdv($pdv){
        $oConnexion = self::getConnexion();
        $sql = "UPDATE personnage SET pdv_perso = '".$pdv."' WHERE id_perso = ".$this->_iId;
        $oConnexion->exec($sql);
        $this->_sPdv = $pdv;
    }
}
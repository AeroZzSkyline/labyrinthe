<?php 

require 'flight/Flight.php';
require_once('Personnage.class.php');
require_once('Donjon.class.php');

Flight::route('/', function(){
    echo 'hello world!';
});

Flight::route('/ping', function(){
    echo "pong";
});

Flight::route('GET /donjons', function(){
    $oDonjon = new Donjon(1);
    $aListeDonjon = Donjon::All();
    foreach($aListeDonjon as $aDonjon){
        echo $aDonjon->getIdDonjon.' = '. $aDonjon->getNomDonjon;
    }
});

Flight::route('PUT /perso', function(){

    if(!isset(Flight::request()->data->nom_perso)) Flight::halt(406, "Il manque le nom du personnage");
    if(!isset(Flight::request()->data->pdv_perso)) Flight::halt(406, "Il manque les points de vie du personnage");
    //appel à l'objet personnage
    //création de l'objet personnage
    //Flight request pour recupérer les données envoyés
    $sNom = Flight::request()->data->nom_perso;
    $iPdv = Flight::request()->data->pdv_perso;
    //récupération des paramètres de la route
    //Flight::request()->data données en JSON mis sous forme d'objet

    $oPerso = Personnage::CreerPerso($sNom, $iPdv);
    
    //retourner un json avec id_perso (depuis l'objet)
    Flight::json(
        array(
            "id_perso"  => $oPerso->getId(),
            "nom_perso" => $oPerso->getNom(),
            "pdv_perso" => $oPerso->getPdv()
        )
    );

});

#PLACE UN JOUEUR A L'ENTREE DU DONJON 
Flight::route('POST /perso/@id_perso/donjon/@id_donjon',function($iIdPerso, $iIdDonjon){
    //On charge le perso
    $oPerso = new Personnage($iIdPerso);
    
    //On positionne le perso dans la piece d'entrée du donjon / on récupère son id (pièce entrée)
    $iIdPieceEntree = $oPerso->PlacerEntree($iIdDonjon);

    //si IdEntreepièce renvoie false (joueur déjà placé)
    if($iIdPieceEntree == false){Flight::halt(405 , "Le joueur est déjà placé a l'entrée du donjon");}
    
    $aPiecesPossibles = $oPerso->PiecesPossible($iIdPieceEntree);
    //On récupère les pièces possible
        Flight::json(array(
            "id_piece" => $iIdPieceEntree,
            "id_piece_possibles" => $aPiecePossibles //ex: array(89,43,52)
        ));
});


#SI IL PEUT (VERIFIER POSSIBILITE), le joueur entre dans une piece
Flight::route('POST /perso/@id_perso/piece/@id_piece',function($iIdPerso, $iIdPiece){
    //Voir dans quelle piece est le personnage
    $oPerso = new Perso($iIdPerso);
    $IdPieceActuelle = $oPerso->VerifPiece($iIdPerso);
    $aPiecesPossibles = $oPerso->PiecesPossible($IdPieceActuelle);

    //Verifier si la piece passée en paramètre est une piece possible de la piece actuelle
    $bPossibleAvacancer = $oPerso->VerifAvancer($IdPieceActuelle, $iIdPiece);

    if($bPossibleAvacancer){
        //Placer le personnage dans la nouvelle pièce(insert dans parcours)
        $oPerso->AvancerPiece($iIdPiece);

        Flight::json(array(
            "id_piece" => $IdPieceActuelle,
            "id_piece_possibles" => $aPiecesPossibles,
            "id_piece_precedente" => 56,
    
            "monstre" => [
                "id_monstre" => 76,
                "nom_monstre" => 'Blop',
                "pdv_monstre" => 60
            ],
    
           "id_coffre" => 75
        ));
    }else{
        Flight::halt(400,"Tu essaie d'avancer dans une pièce qui n'est pas une piece possible");
    }

    

   
});

#SI LUTILISATEUR OUVRE UN coffre
Flight::route('POST /perso/@id_perso/piece/@id_piece/coffre/@id_coffre/ouvrir',function($iIdPerso, $iIdPiece, $iIdCoffre){
    //On regarde si le coffre passé en param est dans la pièce

    //Est ce qu'il a déjà été ouvert

    //Est ce qu'il est piégé

    //trésor dedans?

    Flight::json(array(
        "id_coffre" => 67,
        "piege_coffre" => 3,
        "tresor_coffre" => 'Excalibur'
    ));
});

#Donne les infos sur le coffre
Flight::route('GET /perso/@id_perso/piece/@id_piece/coffre/@id_coffre/ouvrir',function($iIdPerso, $iIdPiece, $iIdCoffre){
    //on regarde s'il y a le coffre

    //renvoyer les infos du coffre
    Flight::json(array(
        "id_coffre" => 67,
        "piege_coffre" => 3,
        "tresor_coffre" => 'Excalibur'
    ));
});


#Si l'utilisateur choisit de combattre
Flight::route('POST /perso/@id_perso/piece/@id_piece/monstre/@id_monstre/combattre',function($iIdPerso, $iIdPiece, $iIdMonstre){
    //Verifier si le joueur est dans une piece avec un monstre
        //Recuperer la piece
    $oPiece = new Piece($iIdPiece);
        //Regarder si il y a un monstre dans cette piece
    $bVerifMonstrePiece = $oPiece->VerifMonstrePiece(); // a coder
    if($bVerifMonstrePiece){
            //Verifier si le monstre à déjà été combattu
        $bDejaCombattu = $oPiece->VerifMonstreCombattu(); // a coder
        if($bDejaCombattu){
            //Combat
                //On récupère le perso
            $oPerso = new Personnage($iIdPerso);
                //Combat qui renvoie les pdv restants
            $iPdvRestant = $oPerso->CombattreMonstre($iIdMonstre); //a coder

            Flight::json(array(
                "pdv_perso" => $iPdvRestant
            ));
        }else{
            Flight::halt(400,"Le monstre a déjà été combattu");
        }
    }
    else{
        Flight::halt(400,"Il n'y a pas de monstre dans la pièce");
    }
});


# Si l'utilisateur fuit on lui enleve des points de vie aléatoire entre 1 et 10
Flight::route('POST /perso/@id_perso/piece/@piece/monstre/@id_monstre/fuir',function($iIdPerso, $iIdPiece, $iIdMonstre){
    //si il y a montre

    //si on l'a déjà combattu

    //fuir

    Flight::json(array(
        "pdv_perso" => 95
    ));
});

Flight::route('',function(){
    
});
    
Flight::route('',function(){
        
});
    
Flight::route('',function(){

});

Flight::start();
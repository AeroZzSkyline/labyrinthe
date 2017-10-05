<?php

abstract class LabyrDefault{

    protected static function getConnexion(){
        return new PDO('mysql:unix_socket=/tmp/mysql.sock;dbname=labyr', "root", "far2017");
    }

}
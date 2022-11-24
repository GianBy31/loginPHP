<!DOCTYPE html>
<html>
    <head>
        <link rel='stylesheet' href='style.css'>
    </head>
    <body>
        
    </body>
</html>

<?php
    /* 
        Cerca all'interno dell'array di utenti dato il nome Utente il nome e il cognome e ne restituisce una string
    */
    function searchData($data,$email) {
        foreach($data['userlist'] as $mydata)
        {
            if($mydata['mail'] == $email )
                return $mydata['nome'] . " ".$mydata['cognome'];
        } 
        return "null";
    }
    /*
        Cerca all'interno dell'array di utenti la foto profilo dato il nome utente e ne restituisce il percorso
    */
    function searchFoto($data,$email){
        foreach($data['userlist'] as $mydata)
        {
            if($mydata['mail'] == $email)
                return $mydata['foto'];
        } 
        return "res/default.png";
    
    }

    session_start();

    $json = file_get_contents('userlist.json');
    $data = json_decode($json,true);
    $var = "<div class='wrapper'><h1> Benvenuto ".searchData($data, $_SESSION['nome'])."</h1></div>";
    $var .= "<center><img src='".searchFoto($data, $_SESSION['nome'])."' width='900px'></center>";
    echo $var;
    
?>

<html lang="ita">
    <head>
        <link rel='stylesheet' href='style.css'>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
        <title>Login</title>
    </head>
</html>

<?php
    /*
        Cerca un utente all'interno di un array.
        Restituisce true se è presente 
    */
    function searchUser($data, $email, $name, $surname) {
        foreach($data['userlist'] as $mydata)
        {
            if( $mydata['mail'] == $email || ($mydata['nome'] == $name && $mydata['cognome'] == $surname) )
                return false;
        } 
        return true;
    }
    /*
        Controlla se le credenziali dell'utente sono corrette
        Restituisce:
            0 se l'utente non esiste
            1 se i campi sono corretti
            2 se la password è sbagliata
    */
    function checkUser($data,$email,$pass) {
        foreach($data['userlist'] as $mydata)
        {
            if($mydata['mail'] == $email )
                if($mydata['pass'] == $pass)
                    return 1;
                else
                    return 2;
        } 
        return 0;
    }

    session_start();

    $CNT = mysqli_connect("localhost","root", "","generico");


    $json = file_get_contents('userlist.json');
    //converte il file json in un array
    $data = json_decode($json,true);

    //creo la pagina
    $var = "<div class='container'> <div class='wrapper'>";
    //se l'utente è nella pagine login stampo il form del login
    if(isset($_REQUEST['log'])){
        $var .= "<div class = 'title'><span>Login</span></div>";
        $var .="<form method='post' action='index.php?enter=1&log=1' ENCTYPE='multipart/form-data'>";
        $var .= "<div class='row'>
                <i class='fas fa-user'></i>
                <input type='text' placeholder='email' name='mail' required>
            </div>
            <div class='row'>
                <i class='fas fa-lock'></i>
                <input type='password' placeholder='Password' name='pass' required>
            </div>";
    }else{ //altrimenti della registrazione
        $var .= "<div class = 'title'><span>Registrazione</span></div>";
        $var .="<form method='post' action='index.php?reg=1' ENCTYPE='multipart/form-data'>";
        $var .= "<div class='row'>
                <i class='fas fa-user'></i>
                <input type='text' placeholder='email' name='mail' required>
            </div>
            <div class='row'>
                <i class='fas fa-user'></i>
                <input type='text' placeholder='Nome' name='nome' required>
            </div>
            <div class='row'>
                <i class='fas fa-user'></i>
                <input type='text' placeholder='Cognome' name='cognome' required>
            </div>
            <div class='row'>
                <i class='fas fa-lock'></i>
                <input type='password' placeholder='Password' name='pass' required>
            </div>
            <div class='row'>
                <i class='fas fa-camera-retro'></i>
                <input type='file' accept='image/*'  name='FileInCarico' required>
            </div>";
    }
    if(!isset($_REQUEST['log'])){
        $var .= "<div class='row button'>
                    <input type='submit' value='Registrati'>
                </div>
                    <div class='signup-link'>Sei gia' registrato? Effettua il <a href='index.php?log=1'>login</a></div>
                    </form></div></div>";
    }else{
        $var .= "<div class='row button'>
            <input type='submit' value='Accedi'>
        </div>
        <div class='signup-link'>Non sei registrato? Effettua la <a href='index.php'>registrazione</a></div>
        </form></div></div>";
    }
    //stampo la pagina
    echo $var;

    //l'utente ha premuto il pulsante di registrazione
    if(isset($_REQUEST['reg'])){   
        //verifico che il nome utente non sia già presente, altrimenti stampo errore
        if(searchUser($data, $_POST['mail'],$_POST['nome'],$_POST['cognome']))
        {
            //salvo gli attributi della foto dell'utente in due variabili temporanee 
            $NomeFile=$_FILES['FileInCarico']['name']; //Nome
            $PathTmpFile=$_FILES['FileInCarico']['tmp_name']; //Percorso (che sarà posteggiato in un percorso temporaneo)
            
            //sposto la foto profilo nella cartella res
            move_uploaded_file($PathTmpFile,"./res/".$NomeFile);
            
            //creo un array di supporto dove inserisco i dati dell'utente
            $newar = array(
                    'mail' =>$_POST['mail'] ,
                    'pass' =>$_POST['pass'] ,
                    'nome' =>$_POST['nome'] ,
                    'cognome' =>$_POST['cognome'],
                    'foto' => "./res/".$NomeFile  //percoso immagine

            );

            //inserisco i dati nel database
            $sql = "INSERT INTO users(name,surname,password,photoSrc,email)
                    VALUES ('{$_POST['nome']}','{$_POST['cognome']}','{$_POST['pass']}', './res/$NomeFile','{$_POST['mail']}')";

            if (mysqli_query($CNT, $sql)) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($CNT);
            }

            mysqli_close($CNT);

            //aggiungo all'array contentente tutti gli utenti l'utente appena creato e lo carico nel file json
            array_push($data['userlist'], $newar);
            $json = json_encode($data,JSON_PRETTY_PRINT);
            file_put_contents('userlist.json', $json);

            $_SESSION['nome'] = $_POST['mail'];

            //carico la pagina dell'accesso per mostrare i dati dell'utente
            //header("Location: access.php");
        }else 
            echo "<center> <p class='bar err'>Utente già registrato</p> <center>";
    }
    
    //se l'utente fa l'accesso
    if(isset($_REQUEST['enter'])){  
        //controllo che l'utente non sia già registrato
        $check = checkUser($data, $_POST['mail'], $_POST['pass']);
        if($check == 1)
        {
            $_SESSION['nome'] = $_POST['mail'];
            header("Location: access.php");
        }else if($check == 2)
            echo "<center> <p class='bar err'>Password non corretta</p> </center>";
        else 
            echo "<center> <p class='bar err'>Utente non registrato</p> </center>";
    }
?>


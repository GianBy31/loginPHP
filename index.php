<html lang="ita">
    <head>
        <link rel='stylesheet' href='style.css'>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
        <title>Login</title>
    </head>
</html>

<?php

    session_start();

    //db access variables
    $servername = "localhost";
    $username = "root";
    $password = "";

    $_SESSION['servername'] = $servername;
    $_SESSION['username'] = $username;
    $_SESSION['password'] = $password;

    //connessione al database
    $conn = mysqli_connect($servername,$username, $password);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Crea il database
    $sql = "CREATE DATABASE IF NOT EXISTS generico";
    if (mysqli_query($conn, $sql) == FALSE)
        echo "Error creating database: " . $conn->error;

    $conn = mysqli_connect($servername,$username, $password,"generico");

    //creazione tabella users
    mysqli_query($conn,"create table if not exists users(
                                email    varchar(50)  not null primary key,
                                name     varchar(50)  not null,
                                surname  varchar(50)  not null,
                                password varchar(255) not null,
                                photoSrc varchar(100) not null
                            );");
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
        $sql = "SELECT * FROM users WHERE email = '{$_POST['mail']}'";
        if(mysqli_num_rows(mysqli_query($conn, $sql)) == 0)
        {
            echo mysqli_num_rows(mysqli_query($conn, $sql)) ;
            //salvo gli attributi della foto dell'utente in due variabili temporanee 
            $NomeFile=$_FILES['FileInCarico']['name']; //Nome
            $PathTmpFile=$_FILES['FileInCarico']['tmp_name']; //Percorso (che sarà posteggiato in un percorso temporaneo)
            
            //sposto la foto profilo nella cartella res
            move_uploaded_file($PathTmpFile,"./res/".$NomeFile);

            //inserisco i dati nel database
            $psw = password_hash($_POST['pass'],PASSWORD_DEFAULT);
            $sql = "INSERT INTO users(name,surname,password,photoSrc,email)
                    VALUES ('{$_POST['nome']}','{$_POST['cognome']}','{$psw}', './res/$NomeFile','{$_POST['mail']}')";

            if (mysqli_query($conn, $sql)) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }

            mysqli_close($conn);

            $_SESSION['mail'] = $_POST['mail'];

            //carico la pagina dell'accesso per mostrare i dati dell'utente
            header("Location: access.php");
        }else 
            echo "<center> <p class='bar err'>Utente già registrato</p> <center>";
    }
    
    //se l'utente fa l'accesso
    if(isset($_REQUEST['enter'])){  
        //controllo che le credenziali siano corrette
        $sql = "SELECT email, password FROM users WHERE email = '{$_POST['mail']}'";
        $check = mysqli_num_rows(mysqli_query($conn, $sql));
        if($check == 1)
        {
            $_SESSION['mail'] = $_POST['mail'];
            $row = mysqli_fetch_assoc(mysqli_query($conn, $sql));
            if(password_verify($_POST['pass'], $row['password']))
                header("Location: access.php");
            else
                echo "<center> <p class='bar err'>Password errata</p> </center>";
        }
            echo "<center> <p class='bar err'>Utente non registrato</p> </center>";
    }
?>


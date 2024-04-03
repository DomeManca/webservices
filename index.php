<?php

// Connessione al database
$servername = "localhost";
$username = "program";
$password = "777";
$dbname = "esempio_webservice";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica della connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

//echo $_SERVER['REQUEST_URI'];

$array = explode('/',$_SERVER['REQUEST_URI']);


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (count($array) == 3 && $array[2] != '')
    {
        // Se è specificato un ID nella richiesta GET
        $id = $array[2];
        $sql = "SELECT * FROM dati WHERE id = $id";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode($row);
        } else {
            echo "Nessun risultato trovato con ID $id";
        }
    } 
    else if(count($array) == 3 && $array[2] == '')
    {
        // Se non è specificato un ID nella richiesta GET
        $sql = "SELECT * FROM dati";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $rows = array();
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            echo json_encode($rows);
        } else {
            echo "Nessun risultato trovato nella tabella.";
        }
    }
    else
    {
        // Se il metodo HTTP non è GET
        http_response_code(405); // Metodo non consentito
        echo "Metodo non consentito";
    }
} 
else 
{
    // Se il metodo HTTP non è GET
    http_response_code(405); // Metodo non consentito
    echo "Metodo non consentito";
}

$conn->close();

?>

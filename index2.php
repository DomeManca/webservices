<?php

// Connessione al database
$servername = "localhost";
$username = "program";
$password = "777";
$dbname = "esempio_webserver";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica della connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$array = explode('/',$_SERVER['REQUEST_URI']);

// Gestione delle richieste
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
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
        break;
    
    case 'POST':
        // POST /dati
        // Assumendo che i dati vengano inviati tramite JSON
        $data = json_decode(file_get_contents("php://input"), true);
        $columns = implode(", ", array_keys($data));
        $values = "'" . implode("', '", array_values($data)) . "'";
        $sql = "INSERT INTO dati ($columns) VALUES ($values)";
        
        if ($conn->query($sql) === TRUE) {
            echo "Nuovo record creato con successo.";
        } else {
            echo "Errore durante l'inserimento del record: " . $conn->error;
        }
        break;
    
    case 'PUT':
        // PUT /dati/123
        $id = $_GET['id'];
        // Assumendo che i dati vengano inviati tramite JSON
        $data = json_decode(file_get_contents("php://input"), true);
        $set_values = "";
        foreach ($data as $key => $value) {
            $set_values .= "$key = '$value', ";
        }
        $set_values = rtrim($set_values, ", ");
        $sql = "UPDATE dati SET $set_values WHERE id = $id";
        
        if ($conn->query($sql) === TRUE) {
            echo "Record aggiornato con successo.";
        } else {
            echo "Errore durante l'aggiornamento del record: " . $conn->error;
        }
        break;
    
    case 'DELETE':
        // DELETE /dati/123
        $id = $_GET['id'];
        $sql = "DELETE FROM id WHERE id = $id";
        
        if ($conn->query($sql) === TRUE) {
            echo "Record eliminato con successo.";
        } else {
            echo "Errore durante l'eliminazione del record: " . $conn->error;
        }
        break;
    
    default:
        // Metodo non consentito
        http_response_code(405);
        echo "Metodo non consentito";
}

$conn->close();

?>
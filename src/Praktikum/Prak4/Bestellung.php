<?php declare(strict_types=1);
error_reporting(E_ALL);
// UTF-8 marker äöüÄÖÜß€

require_once './Page.php';

class Bestellung extends Page
{

    protected function __construct()
    {
        parent::__construct();
    }


    public function __destruct()
    {
        parent::__destruct();
    }

    protected function getViewData(): array
    {
        $sql = "SELECT* FROM article";
        $recordSet = $this->_database->query($sql);
        if(!$recordSet) {
            throw new Exception("keine Article in der Datenbank");
        }
        $article_List = array();

        while ($record = $recordSet->fetch_assoc()) {
            $article_id = $record["article_id"];
            $name = $record["name"];
            $picture = $record["picture"];
            $price = $record["price"];
            $article_List[] = array(
                "article_id" => $article_id,
                "name" => $name,
                "picture" => $picture,
                "price" => $price
            );
        }

        $recordSet->free();
        return $article_List;
    }

   protected function generateView(): void
    {
    // Get the data from getViewData() method
    $articleList = $this->getViewData();

    echo <<<HTML
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PizzaShop</title>
    </head>
    <body>
    <nav>
        <a href="Uebersicht.php">Uebersicht</a>
        <a href="Bestellung.php">Bestellung</a>
        <a href="Kunde.php">Kunde</a>
        <a href="Baeker.php">Baeker</a>
        <a href="Fahrer.php">Fahrer</a>
        </nav>
        <h1>
            <strong>Bestellung</strong>
        </h1>
        <h2>
            <strong>Speisekarte</strong>
        </h2>
    HTML;

    // Loop through the article list obtained from getViewData()
    foreach ($articleList as $article) {
        echo <<<HTML
            <img
            class="pizza-image"
                width="150"
                height="100"
                src=$article[picture] alt="" title="$article[name]"
                data-price="{$article['price']}" 

            >
            <br>
            <p>{$article['name']}</p>
            <p>{$article['price']}</p>
        HTML;
    }

    echo <<<HTML
        <h2>Warenkorb</h2>

        <form action="Bestellung.php" method="post"  accept-charset="UTF-8">
            <select name="warenkorb[]" id="selectPizza" class="selectPizza" onchange="seePrice()" size="7" multiple>
            </select>

            <p class="priceTotal">Total Price:€</p>
            <input type="hidden" name="totalPrice" id="totalPrice" value="0.00">

            <div>
                <input
                    type="text"
                    id="inputAddress"
                    name="address-input"
                    placeholder="Ihre Adresse"
                    size="20"
                >
            </div>
            <div>
                <button type="reset">Alle Loschen</button>
                <button
                    type="button"
                    onclick="clearSelecOption()"
                >
                    Auswahl Loschen
                </button>
                <button type="submit">Bestellen</button>
            </div>
        </form>

        <br>

        <script src="interact.js"></script>
    </body>
    </html>
    HTML;
}

protected function processReceivedData(): void
{
    parent::processReceivedData();
    

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['warenkorb'])) {
        $address = $this->_database->real_escape_string(trim($_POST['address-input']));
        if (empty($address)) {
            // Address is blank, redirect back to the Bestellung.php page with an error message
            $_SESSION['error_message'] = 'Bitte geben Sie Ihre Adresse ein.';
            header('Location: Bestellung.php');
            exit();
        }
        session_start();
        $address = $this->_database->real_escape_string($_POST['address-input']);
        // Insert into "ordering" table 
        $insertOrderingSQL = "INSERT INTO ordering (address) VALUES ('$address')";
        $this->_database->query($insertOrderingSQL);

        // Get the ordering_id of the inserted row
        $orderingId = $this->_database->insert_id;

        // Store the ordering ID in the session
        $_SESSION['last_ordering_id'] = $orderingId;
        // Insert into "ordered_article" table for each selected article in the warenkorb
        foreach ($_POST['warenkorb'] as $articleName) {
            // Fetch the corresponding article_id based on the selected article name
            $sql = "SELECT article_id FROM article WHERE name = '$articleName'";
            $result = $this->_database->query($sql);
        
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $articleId = $row['article_id'];
        
                // Insert into "ordered_article" table with auto-incremented ordering_id
                $insertOrderedArticleSQL = "INSERT INTO ordered_article (ordering_id, article_id, status) VALUES ('$orderingId', '$articleId', 0)";
                $this->_database->query($insertOrderedArticleSQL);
            }
        }
        

        // PRG PATTERN
        header('Location: Bestellung.php');
        exit();
    }
}



    public static function main():void
    {
        try {
            $page = new Bestellung();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}


Bestellung::main();


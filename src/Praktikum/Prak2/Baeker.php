<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€

require_once './Page.php';

class Baeker extends Page
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
    $sql = "SELECT ordered_article_id, name, status, ordering_time, ordering_id, picture 
            FROM article 
            NATURAL JOIN ordered_article 
            NATURAL JOIN ordering 
            WHERE status < 3 
            ORDER BY ordering_time, ordered_article_id";

    $recordSet = $this->_database->query($sql);

    if (!$recordSet) {
        throw new Exception("Keine Bestellung in Datenbank vorhanden");
    }

    $bestellungArray = [];

    while ($record = $recordSet->fetch_assoc()) {
        $bestellungArray[] = [
            "orderedArticleID" => $record["ordered_article_id"],
            "name" => $record["name"],
            "status" => $record["status"],
            "orderingTime" => $record["ordering_time"],
            "orderingID" => $record["ordering_id"],
            "picture" => $record["picture"],
        ];
    }

    $recordSet->free();
    
    return $bestellungArray;
}

protected function generateView(): void
{
    $pizzas = $this->getViewData(); // Get data from getViewData()

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
        <h1>
            <strong>Bäcker</strong>
        </h1>

        <section>
            <form action="https://echo.fbi.h-da.de/" method="post" accept-charset="UTF-8">
    HTML;

    foreach ($pizzas as $pizza) {
        $bestellungStatus = $pizza['status'];

        echo <<<HTML
            <h2>{$pizza['name']} - Order ID: {$pizza['orderingID']} - Ordered at: {$pizza['orderingTime']}</h2>
            <img
                width="150"
                height="100"
                src=$pizza[picture] alt="" title="$pizza[name]"
            >
            <br>
            <input type="radio" id="bestellt" name="{$pizza['name']}" value="bestellt">bestellt<br>
            <input type="radio" id="imOfen" name="{$pizza['name']}" value="imOfen">Im Ofen<br>
            <input type="radio" id="fertig" name="{$pizza['name']}" value="fertig">fertig<br>
            <br>
    HTML;
    }

    echo <<<HTML
            <input type="submit" value="Submit">
            </form>
        </section>
    </body>
    </html>
    HTML;
}



    protected function processReceivedData():void
    {
        parent::processReceivedData();

    }

    public static function main():void
    {
        try {
            $page = new Baeker();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}


Baeker::main();


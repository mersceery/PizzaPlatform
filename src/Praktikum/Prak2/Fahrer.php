<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€

require_once './Page.php';

class Fahrer extends Page
{

    protected function __construct()
    {
        parent::__construct();
    }


    public function __destruct()
    {
        parent::__destruct();
    }

    protected function getViewData(): array{
    $sql = "SELECT * FROM ordering";
    $recordset = $this->_database->query($sql);
    if (!$recordset) {
        throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
    }

    $result = array();
    while ($record = $recordset->fetch_assoc()) {
        $result[] = [
            "ordering_id" => $record["ordering_id"],
            "address" => $record["address"],
            "ordering_time" => $record["ordering_time"]
        ];
    }

    $recordset->free();
    return $result;
}

    protected function generateView(): void
    {
        $orderDetails = $this->getViewData();
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
                <strong>Fahrer</strong>
            </h1>
    
            <section>
                <form action="https://echo.fbi.h-da.de/" method="post" accept>
        HTML;
    
        foreach ($orderDetails as $order) {
    
            echo <<<HTML
                <h2>Order ID: {$order['ordering_id']}</h2>
                <p><strong>{$order['address']}</strong></p>
                <p><strong>{$order['ordering_time']}</strong></p>
                <br>
                <input type="radio" id="fertig" name="{$order['ordering_id']}" value="fertig">fertig<br>
                <input type="radio" id="unterwegs" name="{$order['ordering_id']}" value="unterwegs">unterwegs<br>
                <input type="radio" id="geliefert" name="{$order['ordering_id']}" value="geliefert">geliefert<br>
                <br>
            HTML;
        }
    
        echo <<<HTML
                <input type="submit" value="Submit" value="status">
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
            $page = new Fahrer();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}


Fahrer::main();


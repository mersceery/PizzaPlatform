<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€

require_once './Page.php';

class Kunde extends Page
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
    // Check if the ordering ID is set in the session
    if (isset($_SESSION['last_ordering_id'])) {
        $lastOrderingId = (int)$_SESSION['last_ordering_id'];

        // Fetch pizzas for the last ordering ID
        $query = "SELECT * FROM `ordered_article`
            INNER JOIN `article` ON `ordered_article`.`article_id` = `article`.`article_id`
            INNER JOIN `ordering` ON `ordered_article`.`ordering_id` = `ordering`.`ordering_id`
            WHERE `ordered_article`.`ordering_id` = $lastOrderingId
            ORDER BY `ordering`.`ordering_id` ASC";

        $recordset = $this->_database->query($query);
        if (!$recordset) {
            throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
        }

        $pizza = array();
        $record = $recordset->fetch_assoc();
        while ($record) {
            $pizza[] = $record;
            $record = $recordset->fetch_assoc();
        }

        $recordset->free();
        return $pizza;
    } else {
        // If the ordering ID is not set, return an empty array
        return array();
    }
}


    protected function generateView(): void
    {
        $data = $this->getViewData();
        $this->generatePageHeader('Kunde');
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
           // Add the status container
           echo '<div id="status-container"></div>';

           echo '<script src="StatusUpdate.js"></script>';

        // Output view of this page
        $this->generatePageFooter();
    }

    protected function processReceivedData(): void
    {
        parent::processReceivedData();
        session_start();
    }

    public static function main(): void
    {
        try {
            $page = new Kunde();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Kunde::main();

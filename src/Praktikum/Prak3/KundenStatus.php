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
    session_start();
    if ($_SESSION) {
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
        $list = $this->getViewData();
        $jsonString = json_encode($list);
        echo $jsonString;
    }

    protected function processReceivedData(): void
    {
        parent::processReceivedData();
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

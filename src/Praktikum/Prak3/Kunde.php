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
        <nav>
            <a href="Uebersicht.php">Uebersicht</a>
            <a href="Bestellung.php">Bestellung</a>
            <a href="Kunde.php">Kunde</a>
            <a href="Baeker.php">Baeker</a>
            <a href="Fahrer.php">Fahrer</a>
        </nav>
        <h1>
            <strong>Kunde</strong>
        </h1>
        HTML;

        // Output view of this page
        foreach ($data as $item) {
            echo <<< HTML
            <h2>Bestellung: {$item['ordering_id']}</h2>
            <h2>{$item['name']} - Order ID: {$item['ordering_id']} - Ordered at: {$item['ordering_time']}</h2>
            <img
                width="150"
                height="100"
                src={$item['picture']} alt="" title="{$item['name']}"
            >
            HTML;

            $status = $item['status'];
            $isBestellt = ($status == 0) ? 'checked' : '';
            $isImOffen = ($status == 1) ? 'checked' : '';
            $isFertig = ($status == 2) ? 'checked' : '';
            $isUnterwegs = ($status == 3) ? 'checked' : '';
            $isGeliefert = ($status == 4) ? 'checked' : '';

            echo <<< HTML
            <form action="Kunde.php" method="post">
                <meta http-equiv="Refresh" content="10; URL=Kunde.php">
                <p>{$item['name']}<p>
                <input type="radio" name="order_status_{$item['ordered_article_id']}" value="bestellt" {$isBestellt} disabled>
                <label for="html">bestellt</label>
                <input type="radio" name="order_status_{$item['ordered_article_id']}" value="im_offen" {$isImOffen} disabled>
                <label for="html">im Offen</label>
                <input type="radio" name="order_status_{$item['ordered_article_id']}" value="fertig" {$isFertig} disabled>                    
                <label for="html">fertig</label>
                <input type="radio" name="order_status_{$item['ordered_article_id']}" value="unterwegs" {$isUnterwegs} disabled>                    
                <label for="html">unterweg</label>
                <input type="radio" name="order_status_{$item['ordered_article_id']}" value="geliefert" {$isGeliefert} disabled>                    
                <label for="html">geliefert</label>
                <input type="hidden" name="ordering_id" value="{$item['ordering_id']}">
                <input type="hidden" name="ordered_article_id" value="{$item['ordered_article_id']}">
            </form>
            HTML;
        }

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

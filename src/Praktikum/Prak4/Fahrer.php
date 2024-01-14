<?php

declare(strict_types=1);
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

    protected function getViewData(): array
    {
        $sql = "SELECT * FROM `ordered_article`
        INNER JOIN `article` ON `ordered_article`.`article_id` = `article`.`article_id`
        INNER JOIN `ordering` ON `ordered_article`.`ordering_id` = `ordering`.`ordering_id`
        WHERE `ordered_article`.`status` < 4";

        $recordset = $this->_database->query($sql);
        if (!$recordset) {
            throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
        }

        $result = array();
        while ($record = $recordset->fetch_assoc()) {
            $result[] = [
                "ordering_id" => $record["ordering_id"],
                "address" => $record["address"],
                "status" => $record["status"],
                "name" => $record["name"],
                "ordering_time" => $record["ordering_time"],
            ];
        }

        $recordset->free();
        return $result;
    }

    protected function generateView(): void
{
    $data = $this->getViewData();
    $this->generatePageHeader('Fahrer');
    
    echo <<<HTML
    <body>
    <nav>
    <a href="Uebersicht.php">Uebersicht</a>
    <a href="Bestellung.php">Bestellung</a>
    <a href="Kunde.php">Kunde</a>
    <a href="Baeker.php">Baeker</a>
    <a href="Fahrer.php">Fahrer</a>
    </nav>
        <h1>
            <strong>Fahrer</strong>
        </h1>
    HTML;

    if (empty($data)) {
        echo "<p>Keine Lieferung anstehend.</p>";
    } else {
        $current_order_id = null;
        $pizza = "";
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['status'] >= 2) {
                if ($current_order_id != $data[$i]['ordering_id']) {
                    if ($current_order_id !== null) {
                        // Print the previous order
                        substr($pizza, 0, -3);
                        $status = $data[$i - 1]['status'];
                        $isFertig = ($status == 2) ? 'checked' : '';
                        $isUnterwegs = ($status == 3) ? 'checked' : '';
                        $isGeliefert = ($status == 4) ? 'checked' : '';
                        $escapedAddress = htmlspecialchars($data[$i - 1]['address'], ENT_QUOTES, 'UTF-8');

                        echo <<<HTML
                        <form id="form{$current_order_id}" action="fahrer.php" method="post">
                            <meta http-equiv="Refresh" content="10; URL=fahrer.php">
                            <label><b>{$escapedAddress}</b></label>
                            <br>
                            <label><b>$pizza</b></label>
                            <br>
                            <label><b>{$data[$i - 1]['ordering_id']}</b></label>
                            <br>
                            <input type="hidden" name="ordering_id" value="{$current_order_id}">
                            <input type="radio" name="status" value="fertig" {$isFertig} onClick="this.form.submit()">
                            <label for="html">fertig</label>
                            <input type="radio" name="status" value="unterwegs" {$isUnterwegs} onClick="this.form.submit()">
                            <label for="html">unterwegs</label>
                            <input type="radio" name="status" value="geliefert" {$isGeliefert} onClick="this.form.submit()">
                            <label for="html">geliefert</label>
                        </form>
                        HTML;

                        // Reset variables for the next order
                        $pizza = "";
                    }
                    $current_order_id = $data[$i]['ordering_id'];
                }

                // Build pizza list for the current order
                $escapedName = htmlspecialchars($data[$i]['name'], ENT_QUOTES, 'UTF-8');
                $pizza .= $escapedName . ", ";
            }
        }

        // Print the last order if it exists
        if ($current_order_id !== null) {
            // Print the previous order
            substr($pizza, 0, -3);
            $status = $data[$i - 1]['status'];
            $isFertig = ($status == 2) ? 'checked' : '';
            $isUnterwegs = ($status == 3) ? 'checked' : '';
            $isGeliefert = ($status == 4) ? 'checked' : '';
            $escapedAddress = htmlspecialchars($data[$i - 1]['address'], ENT_QUOTES, 'UTF-8');

            echo <<<HTML
            <form id="form{$current_order_id}" action="fahrer.php" method="post">
                <meta http-equiv="Refresh" content="10; URL=fahrer.php">
                <label><b>{$escapedAddress}</b></label>
                <br>
                <label><b>$pizza</b></label>
                <br>
                <label><b>{$data[$i - 1]['ordering_id']}</b></label>
                <br>
                <input type="hidden" name="ordering_id" value="{$current_order_id}">
                <input type="radio" name="status" value="fertig" {$isFertig} onClick="this.form.submit()">
                <label for="html">fertig</label>
                <input type="radio" name="status" value="unterwegs" {$isUnterwegs} onClick="this.form.submit()">
                <label for="html">unterwegs</label>
                <input type="radio" name="status" value="geliefert" {$isGeliefert} onClick="this.form.submit()">
                <label for="html">geliefert</label>
            </form>
            HTML;
        }
    }

    $this->generatePageFooter();
}

    protected function processReceivedData(): void
    {
        parent::processReceivedData();

        if (isset($_POST['ordering_id']) && isset($_POST['status'])) {
            $status = $_POST['status'];
            $status = ($status == 'fertig') ? 2 : (($status == 'unterwegs') ? 3 : 4);
            $ordering_id = $this->_database->real_escape_string($_POST['ordering_id']);
            $query = "UPDATE `ordered_article` SET `status` = '$status' WHERE `ordered_article`.`ordering_id` = '$ordering_id'";
            $recordset = $this->_database->query($query);
            if (!$recordset) {
                throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
            }
            header("Location: fahrer.php", true, 303);
            die();
        }
    }

    public static function main(): void
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

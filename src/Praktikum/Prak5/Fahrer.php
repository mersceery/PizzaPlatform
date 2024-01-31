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
            $orderingID = $record["ordering_id"];
            $status = $record["status"];

            // Check if all pizzas in the order are marked as 'fertig' (status 2)
            $pizzaStatusSql = "SELECT status FROM `ordered_article` WHERE `ordering_id` = '$orderingID'";
            $pizzaStatusRecordset = $this->_database->query($pizzaStatusSql);

            $allPizzasFertig = true;
            while ($pizzaRecord = $pizzaStatusRecordset->fetch_assoc()) {
                if ($pizzaRecord["status"] != 2) {
                    $allPizzasFertig = false;
                    break;
                }
            }
            $pizzaStatusRecordset->free();

            if ($allPizzasFertig) {
                // If all pizzas are 'fertig', add the order to the result array
                $result[] = [
                    "ordering_id" => $orderingID,
                    "address" => $record["address"],
                    "status" => $status,
                    "name" => $record["name"],
                    "ordering_time" => $record["ordering_time"],
                ];
            }
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
        // Organize orders by their IDs
        $groupedOrders = [];
        foreach ($data as $order) {
            $groupedOrders[$order['ordering_id']][] = $order;
        }

        // Loop through the grouped orders
        foreach ($groupedOrders as $orderId => $orders) {
            foreach ($orders as $pizzaOrder) {
                $pizzaList = $pizzaOrder['name'];
                $escapedName = htmlspecialchars($pizzaOrder['name'], ENT_QUOTES, 'UTF-8');
                $pizzaList .= $escapedName . ", ";
            }

            $current_order_id = $orderId;

            // Print the form for the current order
            $escapedAddress = htmlspecialchars($orders[0]['address'], ENT_QUOTES, 'UTF-8');
            $status = $orders[0]['status'];
            $isFertig = ($status == 2) ? 'checked' : '';
            $isUnterwegs = ($status == 3) ? 'checked' : '';
            $isGeliefert = ($status == 4) ? 'checked' : '';

            echo <<<HTML
            <form id="form{$current_order_id}" action="fahrer.php" method="post">
                <meta http-equiv="Refresh" content="10; URL=fahrer.php">
                <label><b>{$escapedAddress}</b></label>
                <br>
                <label><b>$pizzaList</b></label>
                <br>
                <label><b>{$current_order_id}</b></label>
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

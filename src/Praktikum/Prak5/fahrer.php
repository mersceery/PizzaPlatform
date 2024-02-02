<?php

declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
/**
 * Class Fahrer for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 *
 * PHP Version 7.4
 *
 * @file     Fahrer.php
 * @package  Page Templates
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 * @version  3.1
 */

// to do: change name 'Fahrer' throughout this file
require_once './Page.php';

/**
 * This is a template for top level classes, which represent
 * a complete web page and which are called directly by the user.
 * Usually there will only be a single instance of such a class.
 * The name of the template is supposed
 * to be replaced by the name of the specific HTML page e.g. baker.
 * The order of methods might correspond to the order of thinking
 * during implementation.
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 */
class Fahrer extends Page
{
    // to do: declare reference variables for members 
    // representing substructures/blocks

    /**
     * Instantiates members (to be defined above).
     * Calls the constructor of the parent i.e. page class.
     * So, the database connection is established.
     * @throws Exception
     */
    protected function __construct()
    {
        parent::__construct();
        // to do: instantiate members representing substructures/blocks
    }

    /**
     * Cleans up whatever is needed.
     * Calls the destructor of the parent i.e. page class.
     * So, the database connection is closed.
     */
    public function __destruct()
    {
        parent::__destruct();
    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is returned in an array e.g. as associative array.
     * @return array An array containing the requested data. 
     * This may be a normal array, an empty array or an associative array.
     */
    protected function getViewData(): array
    {
        $sql = "SELECT * FROM ordered_article
            INNER JOIN article ON ordered_article.article_id = article.article_id
            INNER JOIN ordering ON ordered_article.ordering_id = ordering.ordering_id
            WHERE ordered_article.status < 4";

        $recordset = $this->_database->query($sql);
        if (!$recordset) {
            throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
        }

        $result = array();
        while ($record = $recordset->fetch_assoc()) {
            $orderingID = $record["ordering_id"];
            $status = $record["status"];

            // Check if all pizzas in the order are marked as 'fertig' (status 2) or 'unterwegs' (status 3)
            $pizzaStatusSql = "SELECT status FROM ordered_article WHERE ordering_id = '$orderingID'";
            $pizzaStatusRecordset = $this->_database->query($pizzaStatusSql);

            $allPizzasFertigOrUnterwegs = true;
            while ($pizzaRecord = $pizzaStatusRecordset->fetch_assoc()) {
                if ($pizzaRecord["status"] != 2 && $pizzaRecord["status"] != 3) {
                    $allPizzasFertigOrUnterwegs = false;
                    break;
                }
            }
            $pizzaStatusRecordset->free();

            if ($allPizzasFertigOrUnterwegs) {
                // If all pizzas are 'fertig' or 'unterwegs', add the order to the result array
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

    /**
     * First the required data is fetched and then the HTML is
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if available- the content of
     * all views contained is generated.
     * Finally, the footer is added.
     * @return void
     */
    protected function generateView(): void
    {
        $data = $this->getViewData();
        $this->generatePageHeader('Fahrer Seite', '', 'fahrer.css');
    
        echo <<<HTML
        <script>
            setTimeout(function() {
            location.reload();
            }, 10000);
        </script>
        HTML;
    
        echo <<<HTML
        <section class="content">
            <div>
                <h1>Fahrer</h1>
            </div>
            <hr>
            <div class="topnav">
                <a href="Uebersicht.php">Übersicht</a>
                <a class="active" href="bestellung.php">Bestellung</a>
                <a href="baecker.php">Baecker</a>
                <a href="fahrer.php">Fahrer</a>
                <a href="kunde.php">Kunde</a>
            </div>
        HTML;
    
        $current_order_id = null;
        $pizza = ""; // Accumulator for pizza names in an order
    
        foreach ($data as $item) {
            if ($current_order_id !== $item['ordering_id']) {
                // Output the information of the previous order
                if ($current_order_id !== null) {
                    $this->outputOrderInfo($current_order_id, $pizza);
                }
    
                // Reset variables for the new order
                $current_order_id = $item['ordering_id'];
                $pizza = $item['name'] . ", ";
            } else {
                $pizza .= $item['name'] . ", ";
            }
        }
    
        // Output the information of the last order
        if ($current_order_id !== null) {
            $this->outputOrderInfo($current_order_id, $pizza);
        }
    
        echo <<<HTML
        </section>
        HTML;
    
        $this->generatePageFooter();
    }
    
    
    private function outputOrderInfo($ordering_id, $pizza)
    {
        $pizza = rtrim($pizza, ", ");
        $data = $this->getViewData(); // Refresh the data for the current order
    
        // Find the corresponding data for the current order
        $orderData = array_values(
            array_filter($data, function ($item) use ($ordering_id) {
                return $item['ordering_id'] == $ordering_id;
            })
        )[0];
    
        $status = $orderData['status'];
        $isFertig = ($status == 2) ? 'checked' : '';
        $isUnterwegs = ($status == 3) ? 'checked' : '';
        $isGeliefert = ($status == 4) ? 'checked' : '';
    
        $special_pizza = htmlspecialchars($pizza);
        $special_address = htmlspecialchars($orderData['address']);
        $special_ordering_id = htmlspecialchars($orderData['ordering_id']);
    
        echo <<<HTML
        <section class="order">
            <form id="formid$special_ordering_id" action="fahrer.php" method="post">
                <fieldset>
                    <p>Bestellnummer: $special_ordering_id</p>
                    <p>$special_address</p>
                    <p>$special_pizza</p>
                    <section class="radio">
                        <input type="radio" id="fertig" name="status" value="fertig" {$isFertig} onclick="document.forms['formid$special_ordering_id'].submit();" >
                        <label for="fertig">fertig</label>
                    </section>
                    <section class="radio">
                        <input type="radio" id="unterwegs" name="status" value="unterwegs" {$isUnterwegs} onclick="document.forms['formid$special_ordering_id'].submit();" >
                        <label for="unterwegs">unterwegs</label>
                    </section>
                    <section class="radio">
                        <input type="radio" id="geliefert" name="status" value="geliefert" {$isGeliefert} onclick="document.forms['formid$special_ordering_id'].submit();" >                    
                        <label for="geliefert">geliefert</label>
                    </section>
                    <input type="hidden" name="ordering_id" value="$ordering_id">
                    <br>
                </fieldset>
            </form>
        </section>
        HTML;
    }
    

    /**
     * Processes the data that comes via GET or POST.
     * If this page is supposed to do something with submitted
     * data do it here.
     * @return void
     */
    protected function processReceivedData(): void
    {
        parent::processReceivedData();
        // to do: call processReceivedData() for all members

        if (isset($_POST['ordering_id']) && isset($_POST['status'])) {
            $status = $_POST['status'];
            $status = ($status == 'fertig') ? 2 : (($status == 'unterwegs') ? 3 : 4);
            $ordering_id = $_POST['ordering_id'];
            $query = "UPDATE `ordered_article` SET `status` = '$status' WHERE `ordered_article`.`ordering_id` = '$ordering_id'";
            $recordset = $this->_database->query($query);
            if (!$recordset) {
                throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
            }
            header("Location: fahrer.php", true, 303);
            die();
        }

        // delete in ordering table when status in ordered_article table is 4
        $query = "DELETE FROM `ordering` WHERE `ordering`.`ordering_id` IN (SELECT `ordering_id` FROM `ordered_article` WHERE `status` = 4)";
        $recordset = $this->_database->query($query);
        if (!$recordset) {
            throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
        }

        // delete in ordered_article table when status is 4
        $query = "DELETE FROM `ordered_article` WHERE `status` = 4";
        $recordset = $this->_database->query($query);
        if (!$recordset) {
            throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
        }
    }

    /**
     * This main-function has the only purpose to create an instance
     * of the class and to get all the things going.
     * I.e. the operations of the class are called to produce
     * the output of the HTML-file.
     * The name "main" is no keyword for php. It is just used to
     * indicate that function as the central starting point.
     * To make it simpler this is a static function. That is you can simply
     * call it without first creating an instance of the class.
     * @return void
     */
    public static function main(): void
    {
        try {
            $page = new Fahrer();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            //header("Content-type: text/plain; charset=UTF-8");
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page. 
// That is input is processed and output is created.
Fahrer::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >

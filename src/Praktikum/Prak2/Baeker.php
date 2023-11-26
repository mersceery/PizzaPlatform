<?php

declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
/**
 * Class Baecker for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 *
 * PHP Version 7.4
 *
 * @file     Baeker.php
 * @package  Page Templates
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 * @version  3.1
 */

// to do: change name 'Baeker' throughout this file
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
class Baeker extends Page
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
        $sql = "SELECT ordered_article_id, name, status, ordering_time, ordering_id, picture 
            FROM article 
            NATURAL JOIN ordered_article 
            NATURAL JOIN ordering 
            WHERE status <= 3 
            ORDER BY ordering_time, ordered_article_id";

    $recordSet = $this->_database->query($sql);

    if (!$recordSet) {
        throw new Exception("Keine Bestellung in Datenbank vorhanden");
    }

    $bestellungArray = [];

    while ($record = $recordSet->fetch_assoc()) {
        $bestellungArray[] = [
            "ordered_article_id" => $record["ordered_article_id"],
            "name" => $record["name"],
            "status" => $record["status"],
            "orderingTime" => $record["ordering_time"],
            "ordering_id" => $record["ordering_id"],
            "picture" => $record["picture"],
        ];
    }

    $recordSet->free();
    
    return $bestellungArray;

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
        $this->generatePageHeader('Baeker'); //to do: set optional parameters
        echo <<< HTML
        <nav>
        <a href="Uebersicht.php">Uebersicht</a>
        <a href="Bestellung.php">Bestellung</a>
        <a href="Kunde.php">Kunde</a>
        <a href="Baeker.php">Baeker</a>
        <a href="Fahrer.php">Fahrer</a>
        </nav>
         <h1>
            <strong>Bäcker</strong>
        </h1>

        HTML;
        $current_ordering_id = NULL;
        for ($i = 0; $i < count($data); $i++) {
            if ($current_ordering_id != $data[$i]['ordering_id']) {
                $current_ordering_id = $data[$i]['ordering_id'];
                echo <<< HTML
                <h2>Bestellung: {$data[$i]['ordering_id']}</h2>
                <h2>{$data[$i]['name']} - Order ID: {$data[$i]['ordering_id']} - Ordered at: {$data[$i]['orderingTime']}</h2>
                <img
                    width="150"
                    height="100"
                    src={$data[$i]['picture']} alt="" title="{$data[$i]['name']}"
                >
    HTML;
            }
            $status = $data[$i]['status'];
            $isBestellt = ($status == 0) ? 'checked' : '';
            $isImOffen = ($status == 1) ? 'checked' : '';
            $isFertig = ($status == 2) ? 'checked' : '';
            echo <<< HTML
            
            <form action="Baeker.php" method="post">
                <meta http-equiv="Refresh" content="10; URL=Baeker.php">
                <p>{$data[$i]['name']}<p>
                <input type="radio" name="order_status_{$data[$i]['ordered_article_id']}" value="bestellt" {$isBestellt}>
                <label for="html">bestellt</label>
                <input type="radio" name="order_status_{$data[$i]['ordered_article_id']}" value="im_offen" {$isImOffen}>
                <label for="html">im Offen</label>
                <input type="radio" name="order_status_{$data[$i]['ordered_article_id']}" value="fertig" {$isFertig}>                    
                <label for="html">fertig</label>
                <input type="hidden" name="ordering_id" value="{$data[$i]['ordering_id']}">
                <input type="hidden" name="ordered_article_id" value="{$data[$i]['ordered_article_id']}">
                <input type="submit" name="submit" value="Update">
            </form>
HTML;
        }
        // to do: output view of this page
        $this->generatePageFooter();
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
        // set new status
        if (isset($_POST['submit']) && isset($_POST['ordering_id']) && isset($_POST['ordered_article_id']) && isset($_POST['order_status_' . $_POST['ordered_article_id']])) {
            $ordering_id = $_POST['ordering_id'];
            $ordered_article_id = $_POST['ordered_article_id'];
            $status = $_POST['order_status_' . $ordered_article_id];
            $status = ($status == 'bestellt') ? 0 : (($status == 'im_offen') ? 1 : 2);
            $query = "UPDATE `ordered_article` SET `status` = $status WHERE `ordered_article`.`ordered_article_id` = $ordered_article_id";
            $recordset = $this->_database->query($query);
            if (!$recordset) {
                throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
            }
            header("Location: Baeker.php", true, 303);
            die();
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
            $page = new Baeker();
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
Baeker::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >
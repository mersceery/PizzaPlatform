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
 * @file     Baecker.php
 * @package  Page Templates
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 * @version  3.1
 */

// to do: change name 'Baecker' throughout this file
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
class Baecker extends Page
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
        $pizza = array();
        $query = "SELECT * FROM `ordered_article`
                    JOIN article ON ordered_article.article_id = article.article_id
                    ORDER BY `ordered_article`.`ordering_id` ASC ,`ordered_article`.`ordered_article_id` ASC";
        $recordset = $this->_database->query($query);
        if (!$recordset) {
            throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
        }
        $record = $recordset->fetch_assoc();
        while ($record) {
            $pizza[] = $record;
            $record = $recordset->fetch_assoc();
        }
        $recordset->free();
        return $pizza;
        // to do: fetch data for this view from the database
        // to do: return array containing data
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
        
        $this->generatePageHeader('Baecker Seite', '', 'baecker.css'); //to do: set optional parameters
        echo <<<HTML
        <script>
            setTimeout(function() {
            location.reload();
            }, 10000);
        </script>
        <div class="content">
            <div>
                <h1>Bäcker</h1>
            </div>
            <hr>
            <div class="topnav">
                <a class="active" href="bestellung.php">Bestellung</a>
                <a href="baecker.php">Baecker</a>
                <a href="fahrer.php">Fahrer</a>
                <a href="kunde.php">Kunde</a>
            </div>
        HTML;

        $current_ordering_id = NULL;
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['status'] > 2) {
                continue;
            }
            
            $current_ordering_id = $data[$i]['ordering_id'];
            $special_current_id = htmlspecialchars($current_ordering_id);
            
            $status = $data[$i]['status'];
            $isBestellt = ($status == 0) ? 'checked' : '';
            $isImOffen = ($status == 1) ? 'checked' : '';
            $isFertig = ($status == 2) ? 'checked' : '';
            $special_pizza_name = htmlspecialchars($data[$i]['name']);
            $special_ordered_article_id = htmlspecialchars($data[$i]['ordered_article_id']);
            echo <<< HTML
            <div class = order>
                <form id="formid$special_ordered_article_id" action="baecker.php" method="post">
                    <fieldset>
                    <p>Bestellung $special_current_id: $special_pizza_name</ß2>
                    <div class = radio>
                        <label>Bestellt
                            <input type="radio" name="order_status_{$data[$i]['ordered_article_id']}" value="bestellt" {$isBestellt} onclick="document.forms['formid$special_ordered_article_id'].submit();" >
                        </label>
                    </div>
                    <div class = radio>
                        <label>
                            Im Ofen
                            <input type="radio" name="order_status_{$data[$i]['ordered_article_id']}" value="im_offen" {$isImOffen} onclick="document.forms['formid$special_ordered_article_id'].submit();">
                        </label>
                    </div>
                    <div class = radio>
                        <label>
                            Fertig
                            <input type="radio" name="order_status_{$data[$i]['ordered_article_id']}" value="fertig" {$isFertig} onclick="document.forms['formid$special_ordered_article_id'].submit();">
                        </label>
                    </div>  
                    <input type="hidden" name="ordering_id" value="{$data[$i]['ordering_id']}">
                    <input type="hidden" name="ordered_article_id" value="{$data[$i]['ordered_article_id']}">
                    </fieldset>
                </form>
            </div>
            
            HTML;
        }

        echo <<<HTML
        </div>
        HTML;

        // to do: output view of this page
        $this->generatePageFooter();
    }

    /**
     * Processes the data that comes via GET or POST.
     * If this page is supposed to do something with submitted
     * data do it here.
     * @return void
     * 
     */
    protected function processReceivedData(): void
    {
        parent::processReceivedData();
        // to do: call processReceivedData() for all members
        // set new status
        if (isset($_POST['ordering_id']) && isset($_POST['ordered_article_id']) && isset($_POST['order_status_' . $_POST['ordered_article_id']])) {
            $ordering_id = $_POST['ordering_id'];
            $ordered_article_id = $_POST['ordered_article_id'];
            $status = $_POST['order_status_'. $ordered_article_id];
            $status = ($status == 'bestellt') ? 0 : (($status == 'im_offen') ? 1 : 2);
            $query = "UPDATE `ordered_article` SET `status` = $status WHERE `ordered_article`.`ordered_article_id` = $ordered_article_id";
            $recordset = $this->_database->query($query);
            if (!$recordset) {
                throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
            }
            header("Location: baecker.php", true, 303);
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
            $page = new Baecker();
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
Baecker::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >
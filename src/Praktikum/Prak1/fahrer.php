<?php declare(strict_types=1);


class Fahrer
{

    /**
	 * @return void
     */
    protected function generateView():void
    {  
        $orderDetails = [
            ['name' => 'Andrew Tate', 'price' => '13,00 €', 'address' => 'Wohnungslos. 1', 'order' => 'Margherita, Salami, Hawaii'],
            ['name' => 'Tristan Tate', 'price' => '12,00 €', 'address' => 'Rheinstr. 11', 'order' => 'Salami, Tonno'],
            ['name' => 'Prison Tate', 'price' => '25,00 €', 'address' => 'Kasinostr. 5', 'order' => 'Hawaii, Yummy Yummy']
        ];

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
                        <h2></h2>
                        <p><strong>{$order['name']}, {$order['address']}&nbsp;&nbsp;&nbsp;{$order['price']}</strong></p>
                        <p><strong>{$order['order']}</strong></p>
                        <img
                    width="150"
                    height="100"
                    src="Prak1_Vorbereitung/images/tates_prison.jpg"
                    alt="Pizza Image"
                >
                <br>
                        <input type="radio" id="fertig" name="{$order['name']}" value="fertig">fertig<br>
                        <input type="radio" id="unterwegs" name="{$order['name']}" value="unterwegs">unterwegs<br>
                        <input type="radio" id="geliefert" name="{$order['name']}" value="geliefert">geliefert<br>
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


    /**
	 * @return void
     */
    public static function main():void
    {
        try {
            $page = new Fahrer();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Fahrer::main();
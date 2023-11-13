<?php declare(strict_types=1);


class Kunde
{

    /**
	 * @return void
     */
    protected function generateView():void
    {
        $lieferStatus = [
            ['name' => 'Margherita', 'status' => 'bestellt'],
            ['name' => 'Salami', 'status' => 'im Ofen'],
            ['name' => 'Hawaii', 'status' => 'fertig']
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
                    <strong>Kunde</strong>
                </h1>

                <section>
                    <form action="https://echo.fbi.h-da.de/" method="post" accept>
            HTML;
        foreach ($lieferStatus as $status) {
            echo <<<HTML
                        <img
                    width="150"
                    height="100"
                    src="Prak1_Vorbereitung/images/PizzaImg.png"
                    alt="Pizza Image"
                >
                
                <br>
                       
                <p>{$status['name']}:&nbsp;{$status['status']}</p>


                <br>
            HTML;
        }

        echo <<<HTML
                    <input type="submit" value="Neue Bestellung" value="status">
                                        
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
            $page = new Kunde();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Kunde::main();
<?php declare(strict_types=1);


class Baeker
{

    /**
	 * @return void
     */
    protected function generateView():void
    {
        $pizzas = [
            ['name' => 'Margherita', 'price' => '4,00 €'],
            ['name' => 'Salami', 'price' => '4,50 €'],
            ['name' => 'Hawaii', 'price' => '5,50 €']
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
                    <strong>Bäcker</strong>
                </h1>

                <section>
                    <form action="https://echo.fbi.h-da.de/" method="post" accept>
            HTML;
        foreach ($pizzas as $pizza) {
            echo <<<HTML
                        <h2>{$pizza['name']}</h2>
                        <img
                    width="150"
                    height="100"
                    src="Prak1_Vorbereitung/images/PizzaImg.png"
                    alt="Pizza Image"
                >
                <br>
                        <input type="radio" id="bestellt" name="{$pizza['name']}" value="bestellt">bestellt<br>
                        <input type="radio" id="imOfen" name="{$pizza['name']}" value="imOfen">Im Ofen<br>
                        <input type="radio" id="fertig" name="{$pizza['name']}" value="fertig">fertig<br>

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
            $page = new Baeker();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Baeker::main();
<?php declare(strict_types=1);


class Bestellung
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
                    <strong>Bestellung</strong>
                </h1>
                <h2>
                    <strong>Speisekarte</strong>
                </h2>

            HTML;
        foreach ($pizzas as $pizza) {
            echo <<<HTML
                <img
                    width="150"
                    height="100"
                    src="Prak1_Vorbereitung/images/PizzaImg.png"
                    alt="Pizza Image"
                >
                <br>
                <p>{$pizza['name']}</p>
                <p>{$pizza['price']}</p>
            HTML;
        }

        echo <<<HTML
                <h2>Warenkorb</h2>

                <form action="https://echo.fbi.h-da.de/" method="post" target="_blank">
                    <select name="Pizza" id="selectPizza" class="selectPizza" onchange="seePrice()" size="7" multiple>
                        <option value="margherita">Margherita</option>
                        <option value="salami">Salami</option>
                        <option value="hawaii">Hawaii</option>
                    </select>

                    <p class="priceTotal">Total Price: 0,0 €</p>
                    <input type="hidden" name="totalPrice" id="totalPrice" value="0.00">

                    <div>
                        <input
                            type="text"
                            id="inputAddress"
                            name="address"
                            placeholder="Ihre Adresse"
                            size="20"
                        >
                    </div>
                    <div>
                        <button type="reset">Alle Loschen</button>
                        <button
                            type="button"
                            onclick="clearSelecOption()"
                        >
                            Auswahl Loschen
                        </button>
                        <button type="submit">Bestellen</button>
                    </div>
                </form>

                <br>

                <script src="interact.js"></script>
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
            $page = new Bestellung();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Bestellung::main();
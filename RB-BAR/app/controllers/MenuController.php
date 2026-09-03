<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Controller.php';

class MenuController extends Controller
{
    public function index(): void
    {
        $pageTitle = 'Menú | RB-BAR';
        $pageScript = 'menu.js';
        $categories = ['Todos', 'Cócteles', 'Cervezas', 'Licores', 'Vinos', 'Bebidas sin alcohol', 'Comida'];
        $products = [
            ['id'=>1,'category'=>'Cócteles','name'=>'Neón Sour','description'=>'Pisco, limón y un toque de maracuyá.','price'=>28000,'image'=>'images/producto-neon-sour.jpg','available'=>true,'stock'=>5],
            ['id'=>2,'category'=>'Cócteles','name'=>'Mojito de la casa','description'=>'Ron, hierbabuena y lima fresca.','price'=>24000,'image'=>'images/producto-mojito.jpg','available'=>true,'stock'=>4],
            ['id'=>3,'category'=>'Cervezas','name'=>'Lager artesanal','description'=>'Ligera, fría y de final limpio.','price'=>14000,'image'=>'images/producto-lager.jpg','available'=>true,'stock'=>8],
            ['id'=>4,'category'=>'Cervezas','name'=>'IPA nocturna','description'=>'Aromática, intensa y equilibrada.','price'=>18000,'image'=>'images/producto-ipa.jpg','available'=>false,'stock'=>0],
            ['id'=>5,'category'=>'Licores','name'=>'Whisky reserva','description'=>'Servido en las rocas o al gusto.','price'=>32000,'image'=>'images/producto-whisky.jpg','available'=>true,'stock'=>3],
            ['id'=>6,'category'=>'Licores','name'=>'Gin tonic cítrico','description'=>'Gin, tónica y botánicos frescos.','price'=>27000,'image'=>'images/producto-gin.jpg','available'=>true,'stock'=>5],
            ['id'=>7,'category'=>'Vinos','name'=>'Copa de tinto','description'=>'Una copa para conversar sin prisa.','price'=>22000,'image'=>'images/producto-vino-tinto.jpg','available'=>true,'stock'=>6],
            ['id'=>8,'category'=>'Vinos','name'=>'Copa de blanco','description'=>'Fresco y elegante para la noche.','price'=>22000,'image'=>'images/producto-vino-blanco.jpg','available'=>true,'stock'=>4],
            ['id'=>9,'category'=>'Bebidas sin alcohol','name'=>'Tropical fizz','description'=>'Fruta fresca, soda y mucho sabor.','price'=>16000,'image'=>'images/producto-tropical.jpg','available'=>true,'stock'=>7],
            ['id'=>10,'category'=>'Bebidas sin alcohol','name'=>'Limonada de coco','description'=>'Cremosa, cítrica y refrescante.','price'=>15000,'image'=>'images/producto-limonada.jpg','available'=>true,'stock'=>6],
            ['id'=>11,'category'=>'Comida','name'=>'Mini burgers','description'=>'Tres burgers para compartir.','price'=>34000,'image'=>'images/producto-burgers.jpg','available'=>true,'stock'=>5],
            ['id'=>12,'category'=>'Comida','name'=>'Papas RB','description'=>'Papas crocantes con salsas de la casa.','price'=>19000,'image'=>'images/producto-papas.jpg','available'=>true,'stock'=>2],
        ];
        require __DIR__ . '/../views/menu/menu.php';
    }
}

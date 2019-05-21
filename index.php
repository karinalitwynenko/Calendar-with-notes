<?php
include_once ("model/Model.php"); // klasa Modelu
include_once ("controller/Controller.php"); // klasa Kontrolera
include_once ("view/View.php");     // klasa Widoku

// rozpocznij sesje
session_start();

// jesli uzytkownik jest zalogowany - w sesji istnieje zapisana instancja modelu
if((isset($_SESSION['model']))){
    $model = unserialize($_SESSION['model']); // odzyskaj model z zapisanej sesji
    $controller = new Controller($model); // utworz instancje kontrolera, przekaz odzyskany z sesji obiekt modelu
    $controller->getRequest($_POST); // sprawdz czy nadeslano dane i je przetworz
}
else{ // uzytkownik nie jest zalogowany
    $controller = new Controller(new Model()); // utworz instancje kontrolera, utworz nowy model
    Controller::loginOrRegister($_POST);
}




<?php
class Controller{
    private $model; // obiekt model
    private $view;  // obiekt view

    public function __construct($model=false){
        // jesli przekazano model
        if($model){
            $this->model = $model; // inicjalizuj model obiektem z parametru
            $this->model->renewDBconnection(); // odnow polaczenie z baza danych
        }
        else{
            $this->model = new Model();
        }
    }
    // funkcja przetwarzajaca dane nadeslane na serwer
    public function getRequest($requestData){
        $this->view = new View($this->model); // utworz instancje widoku, przekaz jej Model
        // jesli przeslano dane na serwer
        // z obslugi kalendarza (edycja, dodawanie wpisu, przegladanie)
        if(!empty($requestData)){
            // zmiana miesiaca
            if(isset($requestData['month'])){
                $this->model->setMonth($requestData['month']);
                $_SESSION['model'] = serialize($this->model);
                $this->view->loadCalendar();
            }
            else if(isset($requestData['action'])){
                // edycja lub dodawanie wpisu
                if($requestData['action']=='editNote' && isset($requestData['day']) && isset($requestData['newNote']) ){
                    $this->model->editNote($requestData['day'],$requestData['newNote']);
                    $_SESSION['model'] = serialize($this->model); // zachowaj model w zmiennej sesji
                    //przeladuj kalendarz
                    $this->view->loadCalendar();
                }
                else if($requestData['action']=='deleteNote' && isset($requestData['day'])){
                    $this->model->deleteNote($requestData['day']);
                    $_SESSION['model'] = serialize($this->model); // zachowaj model w zmiennej sesji
                    //przeladuj kalendarz
                    $this->view->loadCalendar();
                }
                else if($requestData['action']=='logout'){
                    session_destroy(); // zamknij sesje
                    Controller::showLoginPage(); // pokaz formularz logowania
                }
            }
        }
        else{ // nie nadeslano danych - uzytkownik zostal dopiero co zalogowany
              // zaladuj strone kalendarza - dla biezacego miesiaca
              $this->view->loadCalendar();
        }
    }

    // statyczna funkcja obslugujaca logowanie oraz rejestracje uzytkownika
    public static function loginOrRegister($requestData){
        // jesli przeslano komplet danych logowania
        if (isset($requestData['zaloguj'])){
            $model= new Model(); // stworz instancje Modelu
            // walidacja formularza(danych tekstowych) oraz weryfikacja logowania (login i haslo)
            if($model->loginUser() != -1){
                // jesli dane logowania sa poprawne - zapisz instancje Modelu w zmiennej sesji
                $_SESSION['model'] = serialize($model);
                header("Refresh:0"); // odswiez strone
            }
            else Controller::showLoginPage(true); // wyswietl strone logowania z informacja o bledzie, jesli dane sa niepoprawne
        }
        else if(isset($requestData['nowe_konto'])){ // jesli wybrano przycisk "zaloz konto"
            Controller::showRegistrationPage(); // wyswietl strone rejestracji
        }
        // jesli na stronie rejestracji wcisnieto przycisk "zarejestruj"
		// kompletnosc danych jest sprawdzana przez model
        else if(isset($requestData['zarejestruj'])){
            // funckja dokonuje walidacji formularza, sprawdza czy podany uzytkownik juz istnieje
            // jesli dane sa poprawne - tworzy nowego uzytkownika
            if(Controller::addNewUser()){
                Controller::showLoginPage(2); // wyswietl strone logowania z informacja o poprawnej rejestracji
            }
            else
                Controller::showRegistrationPage(1); // wyswietl strone rejestracji z informacja o bledzie
        }
        else{
            Controller::showLoginPage();
        }
    }


    // statyczna funkcja ladujaca strone logowania - parametr ustawiony na true,
    // aby wyswietlic informacje o niepoprawnej probie zalogowania
    public static function showLoginPage($msg=false){
        $temp_view = new View();
        $temp_view->loadLoginPage($msg);
    }
    // statyczna funkcja ladujaca strone rejestracji
    public static function showRegistrationPage($msg=false){
        $temp_view = new View();
        $temp_view->loadRegistrationPage($msg);
    }
    // dodaj nowego uzytkownika - zwroc login nowego uzytkownika lub null w przypadku niepowodzenia
    public static function addNewUser(){
        $temp_model = new Model();
        $newUserLogin = $temp_model->registerUser();
        return $newUserLogin;
    }

}
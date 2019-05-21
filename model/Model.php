<?php
include_once ("DB.php"); // klasa bazy danych
include_once('Validation.php'); // klasa walidujaca logowanie uzytkownika

class Model{
    private $year; // rok kalendarza
    private $db; // uchwyt do bazy danych
    private $dbName; // nazwa bazy danych
    private $usersTabName; // nazwa tablicy z uzytkownikami
    private $notesTabName; // nazwa tablicy z wpisami
    // polskie nazwy miesiecy
    private $monthNames = ['Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec','Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'];

    private $date; // biezaca data
    private $days; // liczba dni w miesiacu
    private $month; // numer aktualnie wybranego miesiaca
    private $monthName;
    private $userLogin; // login aktywnego uzytkownika
    private $loggedUserID; // id zalogowanego uzytkownika
    private $notes = []; // tablica z wpisami dla aktualnie wybranego miesiaca


    public function __construct(){
        $this->usersTabName = 'users';
        $this->notesTabName = 'notes';
        $this->year = 2019;
        $this->dbName='calendar';
        $this->db = new DB("localhost", "root", "", $this->dbName);
        $this->userLogin = null;
        $this->loggedUserID = null;

        $this->date = time(); // ustaw date na biezaca
        // ustaw numer miesiaca na biezacy
        $this->month = date('n',$this->date);
        $this->monthName = $this->monthNames[date('n',$this->date)-1];
    }
    // ponow polaczenie z baza danych
    public function renewDBconnection(){
        $this->db = new DB("localhost", "root", "", $this->dbName);
    }
    // funkcja zwraca liczbe dni w danym miesiacu
    public function generateDays($date){
        $i = 28;
        // sprawdz czy w mieisiacu z podanej dany istnieja dni od 28 do 31
        for($i;$i<32;$i++){
            if(!checkdate( date("n",$date), $i, date("Y",$date) ))
                break;
        }
        return $i-1;
    }
    // funkcja zwraca tablice z wpisami z danego miesiaca lub wpis z danego dnia - jesli go podano
    public function selectNotes($day= false){
        $resultArray = [];
        // jesli podano dzien
        if($day!=false){
            $selectNoteQuery = "select note from $this->notesTabName where month=$this->month and user_id=$this->loggedUserID and day=$day;";
        }
        else
            $selectNoteQuery = "select day,note from $this->notesTabName where month=$this->month and user_id=$this->loggedUserID;";
        if($resultArray = $this->db->select($selectNoteQuery)){
           return $resultArray; // zwroc tablice(asocjacyjna) z wpisami
        }
        else return $resultArray;
        }
    // edytuj lub dodaj nowy wpis
    // zwraca 0 jesli prawidlowo dodano nowy rekord
    public function editNote($day,$newNote){
        $changed = false;
        // sprawdz czy wpis juz istnieje - jesli nie stworz nowy rekord
        if(empty($this->db->select("select * from $this->notesTabName where day=$day and month=$this->month and user_id=$this->loggedUserID;"))){
           $this->db->insert($this->notesTabName,$this->loggedUserID,$newNote,$day,$this->month,$this->year);
           $changed = true;
        }
        // jesli istnieje - edytuj rekord
        else {
            $this->db->update("update $this->notesTabName set note='$newNote' where day=$day and month=$this->month and user_id=$this->loggedUserID;");
            $changed = true;
        }
        if($changed){
            // przeladuj wpisy
            $this->notes = $this->selectNotes();
        }
    }
    public function deleteNote($day){
        if($this->db->delete($this->notesTabName,$day,$this->month,$this->loggedUserID)){
            // przeladuj wpisy
            $this->notes = $this->selectNotes();
            return true;
        }
        else return false;
    }

    // funkcja weryfikujaca uzytkownika - ustawia id oraz login zalogowanego uzytkownika
    // zwraca id uzytkownika lub -1 jesli logowanie nie powiodlo sie
    public function loginUser(){
        $id = -1;
        // sprawdz poprawnosc formularza
        $loginFormData = Validation::validateLoginForm();
        if($loginFormData['login']!="" && $loginFormData['passwd']!=""){
            $id = $this->selectUser(); // zwroc poprawne id lub -1
            // ustaw id zalogowanego uzytkownika
            if($id>0){
                $this->setLoggedUserID($id);
                $this->setUserLogin($_POST['login']);
                $this->setMonth($this->month);
            }
            return $id;
        }
    }

    // funkcja walidujaca formularz logowania oraz weryfikujaca tozsamosc uzytkownika - zwraca jego id
    // jesli w parametrze podano login - funkcja zwraca rekord z tablicy uzytkownikow
    public function selectUser($loggedUserLogin = false){
        if(!$loggedUserLogin){
            $id = -1;
            $login = $_POST['login'];
            $passwd = $_POST['passwd'];
            $sql = "select * from  $this->usersTabName where login='$login';";
            // sprawdz poprawnosc wpisanych danych
            if($loginData = Validation::validateLoginForm()){
                if ($records = $this->db->sql($sql)){
                    // zaklada sie, ze nie istnieja dwa takie same loginy - sprawdzane podczas walidacji
                    $hash = $records[0]->passwd; //pobierz zahaszowane haslo uzytkownika
                    //sprawdz poprawnosc podanego hasla
                    if (password_verify($passwd, $hash)){
                        $id = $records[0]->id; //jesli haslo jest poprawne - pobierz id uzytkownika
                    }
                }
            }
            return $id; //id zalogowanego uzytkownika(>0) lub -1, jesli haslo lub login niepoprawne
        }
        else{
            // zwroc rekord(obiekt) z danymi zalogowanego uzytkownika
            $sql = "select * from  $this->usersTabName where login='$loggedUserLogin';";
            if ($records = $this->db->sql($sql)){
                return $records[0];
            }
        }
    }
    public function registerUser(){
        // sprawdz poprawnosc formularza
        $registrationFormData = Validation::validateRegistrationForm();
        // jesli dane poprawnie przeszly walidacje
        if($registrationFormData['login']!=""&&$registrationFormData['name']!=""&&$registrationFormData['surname']!=""&&$registrationFormData['email']!=""&&$registrationFormData['passwd']!=""){
            // sprawdz czy istnieje juz uzytkownik o takim loginie
            if($this->checkLogin($registrationFormData['login'])){
                $enc_passwd = password_hash($registrationFormData['passwd'],PASSWORD_DEFAULT );
                if($this->db->addNewUser($registrationFormData['login'],$registrationFormData['name'],$registrationFormData['surname'],$registrationFormData['email'],$enc_passwd,$this->usersTabName)) {
                    return $registrationFormData['login'];
                }
             }
        }
        return null; // jesli dodawanie uztkownika nie powiodlo sie
    }
    // funkcja sprawdzajaca czy uzytkownik o podanym loginie nie istnieje
    public function checkLogin($login){
       if($this->db->sql("select * from  $this->usersTabName where login = '$login';")){
          return false;
       }
       else return true; // true jesli nie istnieje - mozna zalozyc konto
    }
    // funkcja zwracajaca obiekt z informacjami o uzytkowniku - imie, nazwisko, login
    public function getUserDesc(){
        $record = $this->selectUser($this->getUserLogin());
        return Array($record->name,$record->surname,$record->login);
    }
    // funkcja zwraca polska nazwe miesiaca, jako parametr przyjmuje znacznik czasu
    public function getMonthName(){
        return $this->monthName;
    }

    public function getMonth(){
        return $this->month;
    }

    // funkcja ustawia nazwe miesiaca, wpisy oraz liczbe dni
    // przyjmuje numer miesiaca jako parametr
    public function setMonth($month){
        $this->month = $month;
        // ustal liczbe dni w miesiacu
        $this->date = strtotime("2019-".$month."-1");
        $this->monthName = $this->monthNames[date('n',$this->date)-1];
        $this->days = $this->generateDays($this->date);
        // pobierz tablice z wpisami dla uzytkownika z danego miesiaca
        $this->notes = $this->selectNotes();
    }
    public function getDays(){
        return $this->days;
    }
    public function getDate(){
        return $this->date;
    }
    public function getNotes(){
        return $this->notes;
    }
    public function getUserLogin(){
        return $this->userLogin;
    }
    public function setUserLogin($userLogin){
        $this->userLogin = $userLogin;
    }
    public function getLoggedUserID(){
        return $this->loggedUserID;
    }
    public function setLoggedUserID($loggedUserID){
        $this->loggedUserID = $loggedUserID;
    }
}
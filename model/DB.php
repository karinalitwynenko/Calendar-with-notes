<?php
class DB{
    private $mysqli; //uchwyt do BD

    private $usersTabName; // nazwa tablicy z uzytkownikami
    private $notesTabName; // nazwa tablicy z wpisami
    private $createUsersTableQuery;
    private $createNotesTableQuery;

    public function __construct($serwer, $user, $pass, $baza) {
        $this->usersTabName = 'users';
        $this->notesTabName = 'notes';
        $this->createNotesTableQuery = "create table if not exists $this->notesTabName (user_id int not null, note varchar(255), day int(2),month int(2), year int(4),foreign key(user_id) references users(id));";
        $this->createUsersTableQuery = "create table if not exists $this->usersTabName (id int auto_increment primary key,login varchar(25), name varchar(25), surname varchar(35),email varchar(35), passwd char(60));";
        // nawiaz polaczenie z baza danych
        $this->mysqli = new mysqli($serwer, $user, $pass, $baza);
        // sprawdz polaczenie
        if ($this->mysqli->connect_errno) {
            // printf("Nie udało sie połączyć z serwerem: %s\n",$this->mysqli->connect_error);
            exit();
        }
        // zmien kodowanie na utf8
        if ($this->mysqli->set_charset("utf8")) {
        }
        // stworz tabele users i notes, jesli jeszcze nie istnieja
        $this->createTable($this->createUsersTableQuery);
        $this->createTable($this->createNotesTableQuery);
    }

    // funkcja tworzaca tabele
    public function createTable($sql){
        if($this->mysqli->query($sql)){
            return true;
        }
        else {
            return false;
        }
    }

    // funkcja zwraca tablice obiektow z rekordami uzyskanymi
    // zapytaniem w parametrze $sql
    public function select($sql){
        $rowsArray = [];
        if ($result = $this->mysqli->query($sql)){
            //$ilepol = count($pola); //ile pól
            $rows = $result->num_rows; // liczba zwroconych rekordow
            // przenies rekordy do tablicy $rowsArray
            while ($row = $result->fetch_object()) { // fetch_object()- Returns the current row of a result set as an object
                $rowsArray[$row->day] = $row->note;
            }
            $result->close(); // zwolnij pamiec

        }
        return $rowsArray;
    }
    public function update($sql){
        if($this->mysqli->query($sql)){
            return true;
        }
        else {
            // echo "Błąd podczas uaktualniania rekordu: <br> ". $this->mysqli->error;
            return false;
        }
    }
    public function addNewUser($login,$name,$surname,$email,$enc_passwd,$tab){ // $tab nazwa tablicy z uzytkownikami
            $sql = "insert into $tab values (null,'$login','$name','$surname','$email','$enc_passwd');";

            if ($this->mysqli->query($sql)) {
                return true;
            }
            else {
                echo $this->mysqli->error;
                return false;
            }
    }

    // zwroc wpis z jednego dnia
    public function selectByDay($sql){
        if ($result = $this->mysqli->query($sql)){
            return $result->fetch_object()->note; // fetch_object()- Returns the current row of a result set as an object
            $result->close(); // zwolnij pamiec
        }
        else return 0;


    }
    // uniwersalna funkcja dla zapytan sql
    // zwraca tablice rekordow
    public function sql($sql){
        $recordsArray = [];
        if ($result = $this->mysqli->query($sql)){
            while ($row = $result->fetch_object()){
                array_push($recordsArray, $row);
            }
        }

        return $recordsArray; // zwraca tablice rekordow lub pusta
    }

    public function insert($tab,$loggedUserID,$newNote,$day,$month,$year){
        if ($this->mysqli->query("insert into $tab values($loggedUserID,'$newNote',$day,$month,$year );")){
            return true;
        }
        else
            return false;
    }
    public function delete($tab,$day,$month,$loggedUserID){
        if ($this->mysqli->query("delete from $tab where day=$day and month=$month and user_id=$loggedUserID;")){
            return true;
        }
        else
            return false;
    }
}

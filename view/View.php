<?php

class View{
    private $model;
    public function __construct($model=null){
        if($model)
            $this->model = $model;
        else
            $this->model = new Model();
    }


    // funkcja ladujaca formularz logowania
    public function loadLoginPage($msg){
        $this->loadHeader();
        if($msg==1){
            echo "Niepoprawne dane logowania"."</br>";
        }
        else if ($msg==2){
            echo "Zarejestrowano nowego użytkownika - spróbuj się zalogować: "."</br>";
        }
            echo '<body><div id=\'form_container\' class="w3-hover-shadow w3-mobile" style="width:50%; margin:auto; margin-top:2%; margin-bottom:2%;">
                    <form method="post" action="index.php" class="w3-container" onsubmit="return validateLoginForm()"  >
                        <h2> Zaloguj się:</h2>
                        <label>Login<span id="login_err"> </span></label>
                        <input id="login" class="w3-input" type="text" name="login">
                        
                        <label>Hasło<span id="passwd_err"> </span></label>
                        <input id="passwd" class="w3-input" type="password" name="passwd">
                        
                        <input type="submit" class="w3-button w3-white w3-border w3-round-large w3-margin w3-center" name="zaloguj" value="zaloguj" />
                        <input type="submit" class="w3-button w3-white w3-border w3-round-large w3-margin w3-center" name="nowe_konto" value="załóż konto" onclick="noweKonto()" ">
                    </form>
                  </div> </body>';
        $this->loadHTMLclosingTag();
    }

    // funkcja ladujaca formularz rejestracji
    public function loadRegistrationPage($msg){
        $this->loadHeader();
        if($msg==1){
            echo "Niepoprawne dane rejestracji"."</br>";
        }
        echo
            '<body><div id=\'form_container\' class="w3-hover-shadow w3-mobile" style="width:50%; margin:auto; margin-top:2%; margin-bottom:2%;">
                 <form method="post" action="index.php" class="w3-container" onsubmit="return validateRegistrationForm()" >
                    <h2> Zarejestruj się:</h2>
                    <label>Login<span id="login_err"> </span></label>
                    <input id="login" class="w3-input" type="text" required  name="login">
                    
                    <label>Imię<span id="name_err"> </span></label>
                    <input id="name" class="w3-input" type="text" required  name="name">
        
                    <label>Nazwisko<span id="surname_err"> </span></label>
                    <input id="surname" class="w3-input" type="text" required placeholder="Kowalski" name="surname">
                    
                    <label>E-mail<span id="email_err"> </span></label>
                    <input id="email" class="w3-input" type="email" required placeholder="kowalski@mail.com" name="email">
                    
                    <label>Hasło<span id="passwd_err"> </span></label>
                    <input id="passwd" class="w3-input" type="password" required placeholder="password" name="passwd">
                    
                    <input type="submit" class="w3-button w3-white w3-border w3-round-large w3-margin w3-center" name="zarejestruj" value="zarejestruj" />
                 </form>
             </div> </body>' ;
        $this->loadHTMLclosingTag();
    }

    // funkcja generujaca strukture kalendarza wraz z wpisami
    public function loadCalendar(){
        $monthName = $this->model->getMonthName();
        // $userInfo to tablica 3 elementowa zawierajaca kolejno imie,nazwisko i login uzytkownika
        $userInfo = $this->model->getUserDesc();
        $this->loadHeader();
        echo '<body><div class="wrapper">
                <main>
                    <div class="toolbar">
                        
                        <div class="current-month">
                            <form action="index.php" method="post">
                                <select name="month">
                                    <option value="1">Styczeń</option>
                                    <option value="2">Luty</option>
                                    <option value="3">Marzec</option>
                                    <option value="4" >Kwiecień</option>
                                    <option value="5">Maj</option>
                                    <option value="6">Czerwiec</option>
                                    <option value="7">Lipiec</option>
                                    <option value="8">Sierpień</option>
                                    <option value="9">Wrzesień</option>
                                    <option value="10">Październik</option>
                                    <option value="11">Listopad</option>
                                    <option value="12">Grudzień</option>        
                                    <input type="submit" value="wybierz" action="index.php">                
                                </select> 
                            </form>
                            
                        </div>
                        <h1>';
        echo "$monthName 2019 </h1>";
                    echo '</div>
                    <div class="calendar">
                        <div class="calendar__header">
                            <div>Poniedziałek</div>
                            <div>Wtorek</div>
                            <div>Środa</div>
                            <div>Czwartek</div>
                            <div>Piątek</div>
                            <div>Sobota</div>
                            <div>Niedziela</div>
                        </div>';


        // czesc odpowiedzialna za wyswietlanie notatek w kalendarzu
        //
        echo '<div class="calendar__week">';
        // ustal pierwszy dzien tygodnia w miesiacu, uzuplenij pozostale dni pustymi polami
        for($i=0;$i<date('N',$this->model->getDate())-1;$i++){
            echo "<div class='calendar__day day'>
                        <div class='note' > </div>
                  </div>";
        }
        // wygeneruj edytowalne okienka z dniami miesiaca
        for($j=1;$j<$this->model->getDays()+1;$j++){
            // sprawdz czy istnieje wpis dla danego dnia
            if(array_key_exists((string)$j,$this->model->getNotes())){
                $temp = $this->model->getNotes()[(string)$j]; // zapisz wpis do zmiennej tymczasowej
            }
            else{
                $temp="";
            }
            // div wyswietlajacy jeden tydzien
            if(($j+date('N',$this->model->getDate())-1)%7==1 && $j!=1){
                echo '<div class="calendar__week">';
            }
            // div z numerem dniem - zawiera edytowalny div na wpisy
            // wyswietl zapisana notatke w zmiennej $temp
            echo "<div class='calendar__day day'><b>$j</b> 
                      <div  contenteditable='true' class='note' onblur='noteBlurred(this)' onfocus='noteFocused(this)' > $temp</div>
                  </div>";
            // domknij div wyswietlajacy tydzien
            if(($j+date('N',$this->model->getDate())-1)%7==0){
                echo '</div>';
            }
        }

        echo "</div>
            </main>
            <sidebar>
                <div class='login'> $userInfo[2]</div>
                <div class='userName''>$userInfo[0] $userInfo[1]</div>
               
                <nav class='menu''>
                    <a class='menu__item' href='#'>
                        <i class='menu__icon fa fa-home'></i>
                        <span class='menu__text' onclick='logout()'><b>wyloguj</b</span>
                    </a>
                    
                </nav>
            </sidebar>
        </div></body>";
        $this->loadHTMLclosingTag();
        }
    // funkcja wieswietlajaca poczatek dokumentu html
    public function loadHeader(){
        echo "<!doctype html>
              <html lang=\"pl\">
                <head>
                    <meta charset=\"utf-8\">
                
                    <title>Kalendarz 2019</title>
                
                    <link rel=\"stylesheet\" href=\"styles.css\">
                    <link rel=\"stylesheet\" href=\"w3.css\" />
                    <script src=\"jquery/jquery-3.3.1.js\"> </script>
                    <script src=\"js/ajax_functions.js\"> </script>
                    <script src='validation.js' > </script>
                </head>";
    }

    public function loadHTMLclosingTag(){
        echo "</html>";
    }

}








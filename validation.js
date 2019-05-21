// wyrażenia regularne do walidacji
var login =/^[0-9A-Za-ząęłńśćźżó_-]{2,25}$/;
var Name = /^[A-ZĄĆĘŁŃÓŚŻŹ][a-ząćęłńóśżź]{2,25}$/;
var surname = /^[A-ZĄĆĘŁŃÓŚŻŹ][a-ząćęłńóśżź]{2,35}$/;
var email = /^([a-zA-Z0-9])+([.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-]+)+/;

// zmienna przechowujaca informacje, czy kliknieto w przycisk
// przenoszacy do formularza rejestracji
var clicked = false;

// testuj wyrazenie ze stringiem zawartym w elemencie o podanym id
function check(id,Regex) {
    var element = document.getElementById(id);
    if(!Regex.test(element.value)) return (false);
    else return (true);
}

// funkcja walidujaca formularz logowania
function validateLoginForm(){
    // nie sprawdzaj formularza jesli uzytkownik przeszedl do rejestracji
    if(!clicked){
        var ok=true;
        if (!check("login",login)){
            ok=false;
            document.getElementById("login_err").innerHTML=" (Wpisz poprawnie login!)";
        }
        else document.getElementById("login_err").innerHTML="";
        if (!check("passwd",login)){
            ok=false;
            document.getElementById("passwd_err").innerHTML=" (Wpisz poprawnie hasło!)";
        }
        else document.getElementById("passwd_err").innerHTML="";
        return ok;
    }
}

// funkcja walidujaca formularz rejestracji
function validateRegistrationForm(){
        var ok=true;
        if (!check("login",login)){
            ok=false;
            document.getElementById("login_err").innerHTML=" (Wpisz poprawnie login!)";
        }
        else document.getElementById("login_err").innerHTML="";
        if (!check("name",Name)){
            ok=false;
            document.getElementById("name_err").innerHTML=" (Wpisz poprawnie imię!)";
        }
        else document.getElementById("name_err").innerHTML="";
        if (!check("surname",surname)){
            ok=false;
            document.getElementById("surname_err").innerHTML=" (Wpisz poprawnie nazwisko!)";
        }
        else document.getElementById("surname_err").innerHTML="";
        if (!check("email",email)){
            ok=false;
            document.getElementById("email_err").innerHTML=" (Wpisz poprawnie login!)";
        }
        else document.getElementById("email_err").innerHTML="";
        if (!check("passwd",login)){
            ok=false;
            document.getElementById("passwd_err").innerHTML=" (Wpisz poprawnie hasło!)";
        }
        else document.getElementById("passwd_err").innerHTML="";
        return ok;
}

// jesli wybrano przycisk przenoszacy do fomularza rejestracji
function noweKonto(){
    clicked = true;
}

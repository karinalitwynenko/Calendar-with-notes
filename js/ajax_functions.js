// obiekt przechowujący zawartosc pol edycyjnych
var Text = new Object();
// gdy pole edycji wpisu traci skupienie - edytuj rekord w bazie,
// jesli tresc ulegla zmianie lub dodaj nowy wpis
function noteBlurred(note){
    var parent_node = note.parentNode;
    var temp_note  = note.parentNode.removeChild(note);
    var day = parent_node .textContent; // zapisz numer dnia
    parent_node.appendChild(temp_note); // dolacz usuniety wezel, przywroc strukture dom
    // jesli zedytowano lub dodano nowy wpis
    if(!note.textContent.isEmpty() && note.textContent !== sessionStorage.getItem('noteContent')){
        console.log("wpis przed edycja: " + sessionStorage.getItem('noteContent') );
        // pobranie numeru edytowanego dnia

        // wyslanie nowego wpisu na serwer
        $.ajax({
            type: "POST",
            url: 'index.php',
            data:"action=editNote"+"&day="+day+"&newNote="+note.textContent,
            success:function(data){
                console.log("edycja/dodawanie w porzadku");
                window.location.reload();
                document.write(data);
            }
        });
    }
    // jesli tresc wpisu zostala skasowana z poziomu pola edycyjnego
    else if(note.textContent==="" && (note.textContent !== sessionStorage.getItem('noteContent'))){
        // wyslanie zapytania o usuniecie wpisu
        $.ajax({
            type: "POST",
            url: 'index.php',
            data:"action=deleteNote"+"&day="+day,
            success:function(data){
                console.log("usuwanie w porzadku");
                window.location.reload();
                document.write(data);
            }
        });
    }
}
// gdy pole edycji wpisu zostaje zaznaczone, do obiektu Text zapisywana
// jest aktualna tresc wpisu
function noteFocused(note){
    sessionStorage.setItem("noteContent",note.textContent);
    noteContent = note.textContent;
    console.log("zapisalem: " + noteContent);
}

// funkcja przesylajaca zadanie wylogowania uzytkownika
function logout(){
    $.ajax({
        type: "POST",
        url: 'index.php',
        data:"action=logout",
        success:function(data){
            window.location.reload();
            document.write(data);
        }
    });
}

String.prototype.isEmpty = function() {
    return (this.length === 0 || !this.trim());
};

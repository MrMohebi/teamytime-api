<?php

//require_once "../configs/index.php";


if(isset($_POST['userID'])){
    echo $_POST['userID'];

}


/*
const requestOptions = {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ userID: 'aaaaaaaaaaaaaaa' })
};
fetch('https://time.m3m.dev/api/updateUser.php', requestOptions)
.then(response => console.log(response))
*/
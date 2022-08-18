<?php

require_once "../configs/index.php";

if(isset($_POST['userID'])){
    echo $_POST['userID'];
}


/*
var details = {
    'userID': 'test@gmail.com',
};

var formBody = [];
for (var property in details) {
  var encodedKey = encodeURIComponent(property);
  var encodedValue = encodeURIComponent(details[property]);
  formBody.push(encodedKey + "=" + encodedValue);
}
formBody = formBody.join("&");

fetch('https://time.m3m.dev/api/updateUser.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
  },
  body: formBody
})
*/
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Timing System</title>
</head>
<body dir="rtl">
    <form action="/api/addTiming.php" method="post">
        <label for="users">کاربر:</label>
        <select name="user" id="users">
            <option value="_">انتخاب نشده</option>
        </select>

        <br/>
        <label for="hours">ساعت:</label>
        <input name="hours" id="hours" type="number">

        <br/>
        <input type="submit" value="Submit">
    </form>
</body>


<script>
    async function getUsers(){
        const users = await fetch("/api/getUsers.php")
        const usersJson = await users.json()
        console.log(usersJson);
        const selectUser = document.getElementById("users")
        for (const eUser of usersJson) {
            console.log(eUser);
            const opt = document.createElement("option")
            opt.value = eUser.id;
            opt.textContent = eUser.name;
            selectUser.appendChild(opt);
        }
    }

    getUsers()

</script>

</html>
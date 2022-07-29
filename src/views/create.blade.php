<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zems  Api</title>
</head>
<body>
    <h1>Create Form</h1>
    <form action="/category" method="POST">
        @csrf
        <div>
            <div>Name</div>
            <input type="text" name="name">
        </div>
        <div>
            <div>details</div>
            <input type="text" name="details">
        </div>
        <div>
            <div>icon</div>
            <input type="text" name="icon">
        </div>
        <div>
            <div>status</div>
            <input type="text" name="status">
        </div>
        
        <div><button>Go</button></div>
    </form>
</body>
</html>
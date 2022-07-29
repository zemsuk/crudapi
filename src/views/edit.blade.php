<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zems  Api</title>
</head>
<body>
    <h1>This is Zems api Edit Form</h1>
    <form action="/category/<?php echo $edit->id; ?>" method="POST">
        @csrf
        <div>
            <div>ID</div>
            <input type="text" name="id" value="<?php echo $edit->id; ?>">
        </div>
        <div>
            <div>Name</div>
            <input type="text" name="name" value="<?php echo $edit->name; ?>">
        </div>
        <div>
            <div>details</div>
            <input type="text" name="details" value="<?php echo $edit->details; ?>">
        </div>
        <div>
            <div>icon</div>
            <input type="text" name="icon" value="<?php echo $edit->icon; ?>">
        </div>
        <div>
            <div>status</div>
            <input type="text" name="status" value="<?php echo $edit->status; ?>">
        </div>
        <select name="_method" id="">
            <option>GET</option>
            <option>POST</option>
            <option>PUT</option>
            <option>DELETE</option>
        </select>
        <div><button>Go</button></div>
    </form>
</body>
</html>
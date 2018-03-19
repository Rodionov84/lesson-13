<?php
include("connect.php");

$action = isset($_GET["action"]) ? $_GET["action"] : "";
$id = isset($_GET["id"]) ? intval($_GET["id"])  : 0;
$sort = isset($_POST["sort"]) ? $_POST["sort"] : "";
$is_done = isset($_GET['is_done']) ? $_GET['is_done'] : 0;
$description = "";

if( $action == "done" )
{
    $db->query("UPDATE `tasks` SET `is_done`=1 WHERE `id` = " . $id);

    header("Location: " . $_SERVER["SCRIPT_NAME"]);
    exit();
}
else if( $action == "remove" )
{
    $db->query("DELETE FROM `tasks` WHERE `id` = " . $id);

    header("Location: " . $_SERVER["SCRIPT_NAME"]);
    exit();
}
else if( $action == "edit" )
{
    //$is_done = $db->query("SELECT `is_done` FROM `tasks` WHERE `id` = " . $id)->fetch();
    //$is_done = $is_done['is_done'];
    $description = $db->query("SELECT `description`, `is_done` FROM `tasks` WHERE `id` = " . $id)->fetch();
    $is_done = $description["is_done"];
    $description = $description["description"];
}

if( isset( $_POST["id"] ) )
{
    $id = intval($_POST["id"]);
    $description = $_POST["description"];
    $is_done = intval($_POST['is_done']);

    if( $id == 0 )
    {
        $db->query("INSERT INTO `tasks`(`description`, `date_added`) VALUES ('$description', now())");
    }
    else
    {
        $db->query("UPDATE `tasks` SET `description`='$description', `is_done`='$is_done' WHERE `id` = " . $id);

        header("Location: " . $_SERVER["SCRIPT_NAME"]);
        exit();
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>4.2</title>


    <style>
        form
        {
            display: inline-block;
        }
        table {
            border-spacing: 0;
            border-collapse: collapse;
        }
        table td, table th {
            border: 1px solid #ccc;
            padding: 5px;
        }
        table th {
            background: #eee;
        }

        .done
        {
            color: green;
        }
        .inProcess
        {
            color: chocolate;
        }

        .exist {
            display: inline;
        }

        .notExist {
            display: none;
        }
    </style>

</head>

<body>
<form method="post">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="text" name="description" placeholder="Описание" value="<?php echo $description; ?>">
    <?php if($id) { ?>
    <select name="is_done">
        <option value="0" <?php echo !$is_done ? " selected" : "";?>>В процессе</option>
        <option value="1" <?php echo $is_done ? " selected" : "";?>>Готово</option>            <!-- условие что при редактировании выводить select-->
    </select>                                                                                                                      <!-- edit-->
    <?php } ?>
    <input type="submit" value="<?php echo $id ? "Редактировать" : "Добавить"; ?>">
</form>
<form method="post">
    <label for="sort">Сортировать по:</label>
    <select name="sort">
        <option value="date_added"<?php echo $sort == "date_added" ? " selected" : ""; ?>>Дате добавления</option>
        <option value="is_done"<?php echo $sort == "is_done" ? " selected" : ""; ?>>Статусу</option>
        <option value="description"<?php echo $sort == "description" ? " selected" : ""; ?>>Описанию</option>
    </select>
    <input type="submit" value="Отсортировать">
</form>
<br><br>
<table>
    <thead>
        <tr>
            <th>Описание задачи</th>
            <th>Дата добавления</th>
            <th>Статус</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php
    $sort = $sort != "" ? " ORDER BY `" . $sort . "`" : "";

    $sql = "SELECT * FROM `tasks`" . $sort;
    $query = $db->query($sql);

    if( $query->rowCount() ) {
        foreach ($query as $row) {
            printf("<tr><td>%s</td>
                                    <td>%s</td><td class='%s'>%s</td>
                                    <td><a href='%s?action=edit&id=%d'>Изменить</a> 
                                           %s
                                           <a href='%s?action=remove&id=%d'>Удалить</a></td></tr>",
                $row["description"],
                $row["date_added"],
                $row["is_done"] ? "done" : "inProcess",
                $row["is_done"] ? "Выполнено" : "В процессе",
                $_SERVER['SCRIPT_NAME'],
                $row["id"],
                $row["is_done"] ? "" : "<a href='" . $_SERVER['SCRIPT_NAME'] . "?action=done&id=" . $row["id"] . "''>Выполнить</a>",
                $_SERVER['SCRIPT_NAME'],
                $row["id"]
            );
        }
    }

    ?>
    </tbody>
</table>
</body>
</html>
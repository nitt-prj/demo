<?php 
//trường hợp lúc đầu chưa submit
//check có request hay không 
$tasks = array();
if (isset($_REQUEST['tasks'])) {
  $tasks = $_REQUEST['tasks'];
} 
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Demo tasks</title>
    
    <link href="tasks.css" rel="stylesheet" type="text/css" />
  </head>
  <body>
    <h1><?php echo sizeof($tasks); ?> task</h1>
    <!-- task nhap xong -->
    <ul>
      <?php foreach ($tasks as $key => $task):?>
      <li><?php echo $task; ?> <a href="#" id="task_<?php echo $key; ?>">Delete</a></li>
      <?php endforeach; ?>
    </ul>
    
    <form action="index.php" method="POST">
      <!-- nhap ten -->
      <label>Task name: </label>
      <input type="text" name="tasks[]" />
      
      <!-- button -->
      <?php foreach ($tasks as $key => $task):?>
      <input type="hidden" name="tasks[]" value="<?php echo $task; ?>" />
      <?php endforeach; ?>
      
      <button type="submit">Add tasks</button>
      
      <p id="error"></p>
    </form>
  </body>
</html>

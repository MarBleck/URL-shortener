<?php
  $filename = 'init.php';
 
  if (file_exists($filename)) {
      require_once 'init.php';
      $run = mysqli_query($conn, $sql);
      $run1 = mysqli_query($conn, $sql1);
      $run2 = mysqli_query($conn, $sql2);

      $file_pointer = "init.php";

      // Use unlink() function to delete a file
      if (!unlink($file_pointer)) {
          echo ("$file_pointer cannot be deleted due to an error");
          exit();
      }
      else {
          echo ("$file_pointer has been deleted");
          exit();
      }
  } else {
    header("location: /admin/index.php");
    exit();
  }

  

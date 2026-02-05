<?php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "progresspulse";

   // Create connection
   $conn = new mysqli($servername, $username, $password, $dbname);

   // Check connection
   if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
   }
   echo "Connected successfully";

   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       echo "POST request received!";
   } else {
       echo "This script only handles POST requests.";
   }
   ?>
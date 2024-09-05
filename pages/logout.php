<?php
session_start();

// Destroy the session
session_destroy();

// Display a logout message for a brief moment
echo "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Logging Out...</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }
		
        .message-box {
            text-align: center;
            padding: 20px;
            background-color: #FFF7E1;
            border: 1px solid #8B4513;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
		

        footer p {
            margin: 0;
            color: white;
        }
    </style>
	
</head>
    
<body>
    <div class='message-box'>
        <h2>Logging Out...</h2>
        <p>You are being redirected to the homepage.</p>
    </div>
    <script>
        // Redirect to the homepage after a brief delay
        setTimeout(function() {
            window.location.href = 'index.html';
        }, 2000); // 2 seconds delay
    </script>
</body>
</html>";
?>


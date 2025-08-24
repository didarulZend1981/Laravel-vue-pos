<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel vue point of saling application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            color: white;
            text-align: center;
            padding: 20px 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .card {
            margin: 20px;
            background-color: #eef2f7;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .otp {
            font-size: 28px;
            font-weight: bold;
            color: #ff5722;
            margin: 10px 0;
            padding-left: 5px;
        }
        .instructions {
            font-size: 14px;
            color: #555;
            margin-bottom: 20px;
        }
        .footer {
            background-color: #f9f9f9;
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #888;
        }
        .footer a {
            color: #2575fc;
            text-decoration: none;
        }
        /* Responsive */
        @media (max-width: 768px) {
            .container {
                margin: 10px;
            }
            .header h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your OTP Code</h1>
        </div>
        <div class="card">
            <p class="instructions">Use the OTP code below to verify your email address:</p>
            <p class="otp">{{ $otp }}</p>
            <p class="instructions">This code is valid for 1 minutes. If you didn't request this, please ignore this email.</p>
        </div>
        <div class="footer">
            <p>&copy; 2024 Your Company. All rights reserved. <a href="#">Privacy Policy</a></p>
        </div>
    </div>
</body>
</html>

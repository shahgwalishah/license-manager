<!DOCTYPE html>
<html>
<head>
    <title>License Error</title>
    <meta charset="utf-8">
    <style>
        body {
            background: #f3f3f3;
            text-align: center;
            padding-top: 120px;
            font-family: Arial, sans-serif;
        }
        .box {
            width: 45%;
            background: #fff;
            padding: 40px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        h2 { color: #d9534f; margin-bottom: 10px; }
        p { color: #555; margin-bottom: 5px; }
        small { color: #aaa; }
    </style>
</head>
<body>
<div class="box">
    <h2>🔒 License Error</h2>
    <p>{{ $message ?? 'License key invalid.' }}</p>
    <small>Framework Support</small>
</div>
</body>
</html>

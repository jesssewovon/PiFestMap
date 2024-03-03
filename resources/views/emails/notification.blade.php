<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Notification mail</title>
    <style>

        .content{
            margin: 2% 2%;
        }

        p{
            font-size: 15px;
            color: black;
        }

        .home-button{
            width: 250px;
            height: 60px;
            background-color: #00A996;
            border: 1px solid #FFFFFF;
            border-radius: 5px;
            opacity: 1;
            color: white;
            text-align: center;
            text-decoration: none;
            font-size: 15px;
            margin-top: 20px;
        }

        .home-button a {
            color: inherit;
            text-decoration: none;
        }
        .btn-piketplace{
            background-color: #ec11b5!important;
            color: #fff!important;
        }
        .btn-dark-piketplace{
            background-color: #a63289!important;
            color: #fff!important;
        }
        .btn-dark-piketplace-outline{
            background-color: #fff!important;
            color: #a63289;
            border: 1px solid #a63289;
        }
        .color-piketplace{
            color: #a63289!important;
        }
        .color-yellow-piketplace{
            color: #f6bb42!important;
        }
        .color-white{
            color: white;
        }
        table,th,td {
            padding: 10px;
            border: 1px solid black;
            border-collapse: collapse;
        }
    </style>
</head>
<body>
    <div class="content">
        </br>
        <div style="text-align: center">
            <div>
                <p>
                    {!! $message_text !!}
                </p>
            </div>
            <br>
            <br>
            <div>
                <img style="text-align: center; width: 100px;" src="{{$message->embed($url)}}"/>
            </div>
        </div>
    </div>
</body>
</html>















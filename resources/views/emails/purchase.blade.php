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
        @php
            app()->setLocale($user->locale);
            $buyer = isset($line_orders[0]->order->user)?$line_orders[0]->order->user:$line_orders[0]->pre_order_user;
        @endphp
        </br>
        <div style="text-align: center">
            <div>
                <p>
                    {{ __('email.sale_notification', [
                        'buyer' => $buyer->fullnameOrUsername,
                        'nb_product' => count($line_orders)
                    ]) }}
                </p>
                <!-- <p style="text-align: left;">
                    N° demande: <strong>78-8788</strong><br>
                    Date soumission demande: <strong>2024-11-12</strong><br>
                    Nom: <strong>UJ</strong><br>
                </p> -->
                <table style="width: 100%;border-collapse: collapse;border-radius: 10px;">
                    <thead class="color-white btn-dark-piketplace">
                        <th>{{__('products')}}</th>
                        <th>{{__('email.price')}}</th>
                    </thead>
                    <tbody class="color-piketplace">
                        @php
                            $total = 0;
                        @endphp
                        @foreach($line_orders as $line)
                            @php
                                $total = piket_add_str($line->total, $total);
                            @endphp
                            <tr>
                                <td><strong class="color-yellow-piketplace">{{$line->quantity}}x</strong> - {{$line->libelle}}</td>
                                <td>{{$currency}}{{$line->price_str}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <h3>{{__('email.total_display', ['amount'=>$currency.''.$total])}}</h3>
                <!-- <button class="home-button btn-dark-piketplace-outline">
                    <a href="pi://mainnet.piketplace.com"> {{__('email.check_your_account')}} </a>
                </button> -->
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















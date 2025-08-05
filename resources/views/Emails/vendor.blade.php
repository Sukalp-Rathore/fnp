<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>New Order Assigned</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            background-color: #ffffff;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
        }

        .info {
            margin-top: 20px;
        }

        .info p {
            margin: 5px 0;
            color: #555;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <h2>Hello {{ $details['name'] }},</h2>
        <p>You have been assigned a new order. Below are the order details:</p>

        <div class="info">
            <p><strong>Customer Name :</strong> {{ $details['order_details']['customer_name'] }}</p>
            <p><strong>Customer Email :</strong> {{ $details['order_details']['customer_email'] }}</p>
            <p><strong>Customer Phone :</strong> {{ $details['order_details']['customer_mobile'] }}</p>
            <p><strong>Address:</strong> {{ $details['order_details']['customer_address'] }}</p>
            <p><strong>City:</strong> {{ $details['order_details']['city'] }}</p>
            <p><strong>Event:</strong> {{ $details['order_details']['event_name'] }}</p>
            <p><strong>Delivery Date:</strong> {{ getutc($details['order_details']['delivery_date'], 'd.m.y') }}</p>
            <p><strong>Products:</strong> {{ $details['order_details']['products'] }}</p>
            <p><strong>Message:</strong> {{ $details['order_details']['message'] }}</p>
        </div>

        <div class="footer">
            <p>— Team Flowers n Petals</p>
            <p>📞 9893056096 | 📍 Indore</p>
            <p>🌐 flowersnpetals.net</p>
        </div>
    </div>
</body>

</html>

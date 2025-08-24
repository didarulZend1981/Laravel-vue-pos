
<html>

<head>
    <style>
        .text-center{
            text-align: center;
        }
        .customers {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
            font-size: 12px !important;
        }

        .customers td,
        #customers th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .customers tr:nth-child(even) {
            background-color: #F3F6F9;
        }

        .customers tr:hover {
            background-color: #ddd;
        }

        .customers th {
            padding-top: 12px;
            padding-bottom: 12px;
            padding-left: 6px;
            text-align: left;
            background-color: #ff0062;
            color: white;
        }
    </style>
</head>

<body>

    <h3 class="text-center">ABC shop's purchase report</h3>

    <table class="customers">
        <thead>
            <tr>
                <th>Report</th>
                <th>Date</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Account Payable</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Sales Report</td>
                <td>{{ $FormDate }} to {{ $ToDate }}</td>
                <td>{{ $total }}</td>
                <td>{{ $paid }}</td>
                <td>{{ $account_payable }} </td>
            </tr>
        </tbody>
    </table>


    <h3 class="text-center">Details</h3>
    <table class="customers">
        <thead>
            <tr>
                <th>Supplier</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Company</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Account Payable</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($list as $item)
                <tr>
                    <td>{{ $item->supplier->name }}</td>
                    <td>{{ $item->supplier->phone }}</td>
                    <td>{{ $item->supplier->address }}</td>
                    <td>{{ $item->supplier->brand->name }}</td>
                    <td>{{ $item->total }}</td>
                    <td>{{ $item->paid }}</td>
                    <td>{{ $item->account_payable }}</td>
                    <td>{{ $item->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>

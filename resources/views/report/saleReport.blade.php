
<html>

<head>
     <style>
        /* A4 page setup */
        @page {
            size: A4 portrait;
            margin: 15mm 10mm 15mm 10mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0 10%;
            width: 80%;
            font-size: 12px;
        }

        .text-center {
            text-align: center;
        }

        .customers {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
            font-size: 12px;
        }

        .customers th,
        .customers td {
            border: 1px solid #ddd;
            padding: 6px;
        }

        .customers tr:nth-child(even) {
            background-color: #F3F6F9;
        }

        .customers tr:hover {
            background-color: #ddd;
        }

        .customers th {
            padding: 10px 6px;
            text-align: left;
            background-color: #009CFF;
            color: white;
        }

        /* Print-specific styles */
        @media print {
            body {
               margin: 0 10%;
                font-size: 11px;
            }

            .print-button {
                display: none; /* hide print button when printing */
            }

            table.customers {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }

        /* Print button style */
        .print-button {
            margin: 10px 0;
            padding: 6px 12px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }

        .print-button:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>

    <h3 class="text-center">ABC Shop's sales report</h3>

    <table class="customers">
        <thead>
            <tr>
                <th>Report</th>
                <th>Date</th>
                <th>Total</th>
                <th>Discount</th>
                <th>Vat</th>
                <th>Pay</th>
                <th>Payable</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Sales Report</td>
                <td>{{ $FormDate }} to {{ $ToDate }}</td>
                <td>{{ $total }}</td>
                <td>{{ $discount }}</td>
                <td>{{ $vat }}</td>
                <td>{{ $paid }}</td>
                <td>{{ $payable }} </td>
            </tr>
        </tbody>
    </table>


    <h3 class="text-center">Details</h3>
    <table class="customers">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Total</th>
                <th>Discount</th>
                <th>Vat</th>
                <th>Payable</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($list as $item)
                <tr>
                    <td>{{ $item->customer->name }}</td>
                    <td>{{ $item->customer->phone }}</td>
                    <td>{{ $item->customer->email }}</td>
                    <td>{{ $item->total }}</td>
                    <td>{{ $item->discount }}</td>
                    <td>{{ $item->vat }}</td>
                    <td>{{ $item->payable }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y h:i A') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
        <button class="print-button" onclick="window.print()">Print Report</button>
        <script>
            function formatDate(dateString) {



                const date = new Date(dateString);

                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();

                let hours = date.getHours();
                const minutes = String(date.getMinutes()).padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';

                hours = hours % 12;
                hours = hours ? hours : 12; // 0 হলে 12 দেখাবে

                return `${day}/${month}/${year} ${hours}:${minutes} ${ampm}`;
                }
        </script>
</body>

</html>

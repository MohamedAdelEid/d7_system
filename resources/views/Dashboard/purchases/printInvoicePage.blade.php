<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            direction: rtl;
            text-align: right;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            /* border: 1px solid #eee;
             */
             border: 2px solid #1d0000;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            color: #555;
        }

        
        .invoice-header table,
        .invoice-footer table {
            width: 100%;
            line-height: inherit;
            text-align: right;
        }

        .invoice-header table td,
        .invoice-footer table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-header table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .product-table th,
        .product-table td {
            padding: 5px;
            border: 1px solid #000;
            color: #000;
        }

        .product-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center
        }

        .product-table td {
            text-align: center
        }

        .logo {
            max-width: 150px;
            max-height: 150px;
        }

        .title {
            font-size: 28px;
            font-weight: bold;
        }

        .total-section {
            text-align: right;
            font-weight: bold;
            margin-top: 20px;
        }
        .invoice-footer table td:last-of-type {
          text-align: left;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <!-- Invoice Header -->
        <div class="invoice-header">
            <table style="position: relative;top: 30px;">
                <tr>
                    <td>
                        <strong style="font-weight: 900; color: #000">تاريخ:</strong> {{ $transaction->transaction_date }}<br>
                    </td>
                    <td style="text-align: left;">
                        <strong style="font-weight: 900; color: #000">فاتورة رقم:</strong> {{ $transaction->ref_no }}<br>
                        <strong style="font-weight: 900; color: #000">العميل:</strong> {{ $transaction->Contact->name }}<br>
                        <strong style="font-weight: 900; color: #000">الموبايل: </strong> {{ $transaction->Contact->phone }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="invoice-header" >
            <table>
                <tr>
            
                    <td class="title">
                        <img style="border-radius: 50%; width: 150px ; height: 150px" src="{{ asset('logo.png') }}"
                            alt="">
                    </td>
                </tr>
            </table>
        </div>

        <!-- Product Table -->
        <table class="product-table"  style="margin-top: 20px; margin-bottom: 20px">
            <tr>
                <th>#</th>
                <th>المنتج</th>
                <th>العدد</th>
                <th>الوحده</th>
                <th>السعر</th>
                <th>المجموع</th>
            </tr>
            @foreach ($transaction->TransactionPurchaseLines as $index => $purchase)
            <tr>
                <th>{{ $index }}</th>
                <td>{{ $purchase->product->name }}</td>
                <td>{{ $purchase->quantity }}</td>
                <td>{{ $purchase->Unit->actual_name }}</td>
                <td>{{ $purchase->unit_price }}</td>
                <td>{{ $purchase->unit_price *  $purchase->quantity}}</td>
            </tr>
        @endforeach
        </table>

  
        <div class="invoice-footer">
            <table>
                <tr class="total">
                    <td colspan="4" style="font-size: 20px;font-weight: 900; color: #000">الإجمالي</td>
                    <td>{{ $transaction->total }}</td>
                </tr>
                <tr class="total">
                    <td colspan="4" style="font-size: 20px;font-weight: 900; color: #000">الخصم</td>
                    <td>{{ $transaction->discount_value }}{{ $transaction->discount_type == "percentage" ? "%" : ""  }}</td>
                </tr>
                <tr class="total">
                    <td colspan="4" style="font-size: 20px;font-weight: 900; color: #000">الإجمالي بعد الخصم</td>
                    <td>{{ $transaction->final_price }}</td>
                </tr>
            </table>
        </div>
    </div>
    {{-- <script>
        window.print()
    </script> --}}
</body>
</html>
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
            /* max-width: 800px;
            margin: auto; */
            padding: 20px;
            /* border: 1px solid #eee; */
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

                    <td style="text-align: right;">
                        @if ($settings->display_branch_info_in_invoice)
                            <strong style="font-weight: 900; color: #000">الفرع:</strong>
                            {{ $transaction->Branch->name }}<br>
                        @endif
                        @if ($settings->display_ref_no_in_invoice)
                            <strong style="font-weight: 900; color: #000">فاتورة رقم:</strong>
                            {{ $transaction->ref_no }}<br>
                        @endif
                        @if ($settings->display_created_by_in_invoice)
                            <strong style="font-weight: 900; color: #000">انشأت بواسطة:</strong>
                            {{ $transaction->CreatedBy->name }}<br>
                        @endif

                        @if ($settings->display_contact_info_in_invoice)
                            <strong style="font-weight: 900; color: #000">العميل:</strong>
                            {{ $transaction->Contact->name }}<br>
                            <strong style="font-weight: 900; color: #000">العنوان:</strong>
                            {{ $transaction->Contact->address }}<br>
                            <strong style="font-weight: 900; color: #000">الموبايل: </strong>
                            {{ $transaction->Contact->phone }}
                        @endif
                    </td>
                    @if ($settings->display_invoice_date_in_invoice)
                        <td style="text-align: left">
                            <strong style="font-weight: 900; color: #000">تاريخ:</strong>
                            {{ $transaction->transaction_date }}<br>
                            <img style="border-radius: 50%; width: 150px ; height: 150px;
                                margin-top: 37px;
                                 margin-bottom: 40px;"
                                src="{{ asset(\App\Models\Setting::first()->image_invoice) }}" alt="">
                        </td>
                    @endif
                </tr>
            </table>
        </div>



        <!-- Product Table -->
        <table class="product-table" style="margin-top: 20px; margin-bottom: 20px">
            <tr>
                <th>#</th>
                <th>المنتج</th>
                <th>العدد</th>
                <th>الوحده</th>
                <th>السعر</th>
                <th>المجموع</th>
            </tr>
            <!-- Repeat this block for each item -->
            @foreach ($transaction->TransactionSellLines as $index => $sell)
                <tr>
                    <th>{{ $index + 1 }}</th>
                    <td>{{ $sell->product->name }}</td>
                    <td>{{ $sell->quantity }}</td>
                    <td>{{ $sell->Unit->actual_name }}</td>
                    <td>{{ $sell->unit_price }}</td>
                    <td>{{ $sell->total }}</td>
                </tr>
            @endforeach
            <!-- Add more items as needed -->
        </table>


        <div class="invoice-footer">
            <table>
                @if ($settings->display_total_in_invoice)
                    <tr class="total">
                        <td colspan="4" style="font-size: 20px;font-weight: 900; color: #000">الإجمالي</td>
                        <td>{{ $transaction->total }}</td>
                    </tr>
                @endif
                @if ($settings->display_discount_in_invoice)
                    <tr class="total">
                        <td colspan="4" style="font-size: 20px;font-weight: 900; color: #000">الخصم</td>
                        <td>{{ $transaction->discount_value }}{{ $transaction->discount_type == 'percentage' ? '%' : '' }}
                        </td>
                    </tr>
                @endif
                @if ($settings->display_final_price_in_invoice)
                    <tr class="total">
                        <td colspan="4" style="font-size: 20px;font-weight: 900; color: #000">الإجمالي بعد الخصم</td>
                        <td>{{ $transaction->final_price }}</td>
                    </tr>
                @endif
                @if ($settings->display_credit_details_in_invoice)
                    @if ($transaction->Contact->credit_limit > 0)
                        <tr class="total">
                            <td colspan="4" style="font-size: 20px;font-weight: 900; color: #000">المبلغ المستحق من
                                قبل</td>
                            <td>{{ $totalBeforeDue }}</td>
                        </tr>

                        <tr class="total">
                            <td colspan="4" style="font-size: 20px;font-weight: 900; color: #000">المبلغ المستحق من
                                بعد</td>


                            <td>{{$toalAfterDue }}
                            </td>
                        </tr>
                    @endif
                @endif
            </table>
        </div>
    </div>
    {{-- <script>
        window.print()
    </script> --}}
</body>

</html>

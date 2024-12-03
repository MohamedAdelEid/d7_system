<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #2773</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap');
        
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        
        .invoice-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .invoice-header {
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        
        .invoice-details {
            font-size: 14px;
            color: #666;
        }
        
        .table th {
            background-color: #f8f9fa;
        }
        
        .total-section {
            border-top: 2px solid #eee;
            padding-top: 20px;
            margin-top: 20px;
        }
        
        .contact-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="invoice-title">{{ $settings->site_name }}</h1>
                    <div class="invoice-details mt-3">
                        @if($settings->display_ref_no_in_invoice)
                        <div>رقم فاتورة: {{ $transaction->ref_no }}</div>
                        @endif
                        @if($settings->display_invoice_date_in_invoice)
                        <div>تاريخ : {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y h:i') }}</div>
                        @endif
                    </div>
                    @if($settings->display_created_by_in_invoice)
                    <div>انشأت بواسطة: {{ $transaction->CreatedBy->name }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="customer-details mb-4">
            <div class="row">
                <div class="col-12">
                    @if($settings->display_contact_info_in_invoice)
                    <h5>العميل</h5>
                    <div>{{$transaction->Contact->name}}</div>
                    <div>الموبايل: {{$transaction->Contact->phone}}</div>
                    <div>العنوان: {{$transaction->Contact->address}}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المنتج</th>
                        <th>الكمية</th>
                        <th>الوحده</th>
                        <th>السعر</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaction->TransactionSellLines as $index => $sell)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $sell->product->name }}</td>
                        <td>{{ $sell->quantity }}</td>
                        <td>{{ $sell->Unit->actual_name }}</td>
                        <td>{{ $sell->unit_price }}</td>
                    <td>{{ $sell->total }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="total-section">
            <div class="row">
                <div class="col-md-6 offset-md-6">
                    <table class="table table-borderless">
                        @if($settings->display_total_in_invoice)
                        <tr>
                            <td>الإجمالي:</td>
                            <td class="text-end">{{ $transaction->total }}</td>
                        </tr>
                        @endif
                        @if($settings->display_discount_in_invoice)
                        <tr>
                            <td>الخصم:</td>
                            <td class="text-end">{{ $transaction->discount_value }}{{ $transaction->discount_type == 'percentage' ? '%' : '' }}</td>
                        </tr>
                        @endif
                        @if($settings->display_final_price_in_invoice)
                        <tr>
                            <td>الإجمالي بعد الخصم:</td>
                            <td class="text-end">{{ $transaction->final_price }}</td>
                        </tr>
                        @endif
                        @if($settings->display_credit_details_in_invoice)
                        <tr>
                            <td>المبلغ المستحق من قبل:</td>
                            <td class="text-end">{{ $totalBeforeDue }}</td>
                        </tr>
                        @if($transaction->payment_status == "due")
                        <tr>
                            <td>المبلغ المستحق من بعد:</td>
                            <td class="text-end">{{ $transaction->payment_status == "due" ? $totalBeforeDue + $transaction->final_price : $totalBeforeDue }}</td>
                        </tr>
                        @endif
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="contact-info text-center">
            <p>للشكاوى والاقتراحات الرجاء التواصل</p>
            <p>واتساب</p>
            <p>{{ $transaction->Contact->phone }}</p>
            <p>{{ $transaction->Contact->phone }}</p>
        </div> 
    </div>
</body>
</html>
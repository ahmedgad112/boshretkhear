<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>عقد إيجار - {{ $booking->code }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --forest: #0f4d3a;
            --gold: #c9a227;
            --ink: #1e293b;
            --line: #dbe3dc;
            --muted: #64748b;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Cairo', Tahoma, Arial, sans-serif;
            color: var(--ink);
            background: #eef2f0;
            line-height: 1.7;
        }

        .toolbar {
            position: sticky;
            top: 0;
            z-index: 20;
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
            padding: 14px;
            background: rgba(255,255,255,.95);
            border-bottom: 1px solid var(--line);
            backdrop-filter: blur(8px);
        }

        .toolbar a,
        .toolbar button {
            appearance: none;
            border: 0;
            border-radius: 12px;
            padding: 10px 18px;
            font: inherit;
            font-weight: 800;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-print { background: var(--forest); color: #fff; }
        .btn-back { background: #e2e8f0; color: #0f172a; }

        .sheet {
            width: min(900px, 100%);
            margin: 24px auto 48px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 36px 40px;
            box-shadow: 0 10px 30px rgba(15, 77, 58, .08);
        }

        .header {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: flex-start;
            border-bottom: 3px solid var(--forest);
            padding-bottom: 18px;
            margin-bottom: 22px;
        }

        .brand h1 {
            margin: 0;
            color: var(--forest);
            font-size: 28px;
            font-weight: 800;
        }

        .brand p {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .meta {
            text-align: left;
            font-size: 13px;
            color: var(--muted);
        }

        .meta strong {
            color: var(--forest);
            display: block;
            font-size: 16px;
            margin-bottom: 4px;
        }

        .title {
            text-align: center;
            margin: 8px 0 24px;
        }

        .title h2 {
            margin: 0;
            font-size: 24px;
            color: var(--forest);
        }

        .title p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .intro {
            background: #f4faf7;
            border: 1px solid #d7ebe2;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 22px;
            font-size: 14px;
        }

        h3 {
            margin: 0 0 10px;
            color: var(--forest);
            font-size: 16px;
            border-right: 4px solid var(--gold);
            padding-right: 10px;
        }

        .section { margin-bottom: 22px; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th, td {
            border: 1px solid var(--line);
            padding: 10px 12px;
            vertical-align: top;
        }

        th {
            width: 32%;
            background: #f8faf9;
            color: var(--forest);
            text-align: right;
            font-weight: 800;
        }

        .terms {
            margin: 0;
            padding-right: 20px;
            font-size: 13.5px;
        }

        .terms li { margin-bottom: 8px; }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
            margin-top: 40px;
        }

        .sign-box {
            border-top: 1px dashed var(--line);
            padding-top: 14px;
            text-align: center;
            min-height: 110px;
        }

        .sign-box strong {
            display: block;
            margin-bottom: 48px;
            color: var(--forest);
        }

        .footer {
            margin-top: 28px;
            padding-top: 14px;
            border-top: 1px solid var(--line);
            text-align: center;
            color: var(--muted);
            font-size: 12px;
        }

        @media print {
            body { background: #fff; }
            .toolbar { display: none !important; }
            .sheet {
                width: 100%;
                margin: 0;
                border: 0;
                border-radius: 0;
                box-shadow: none;
                padding: 0;
            }
            a { color: inherit; text-decoration: none; }
        }

        @page {
            size: A4;
            margin: 16mm;
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="btn-print" type="button" onclick="window.print()">طباعة العقد</button>
        <a class="btn-back" href="{{ route('admin.bookings.show', $booking) }}">رجوع لتفاصيل الحجز</a>
    </div>

    <div class="sheet">
        <div class="header">
            <div class="brand">
                <h1>{{ $settings['business_name'] ?? 'بشرى خير' }}</h1>
                <p>{{ $settings['address'] ?? '' }}</p>
                <p>
                    @if(!empty($settings['phone'])) هاتف: {{ $settings['phone'] }} @endif
                    @if(!empty($settings['email'])) &nbsp;|&nbsp; {{ $settings['email'] }} @endif
                </p>
            </div>
            <div class="meta">
                <strong>عقد إيجار</strong>
                رقم العقد: {{ $booking->code }}<br>
                تاريخ التحرير: {{ $printedAt }}
            </div>
        </div>

        <div class="title">
            <h2>عقد إيجار وحدة عقارية</h2>
            <p>تم الاتفاق بين الطرفين على البنود التالية</p>
        </div>

        <div class="intro">
            إنه في يوم {{ $printedAt }} تم الاتفاق بين كل من الطرف الأول (المؤجر) والطرف الثاني (المستأجر)
            على إيجار الوحدة العقارية الموضحة أدناه وفق الشروط والأحكام الواردة بهذا العقد.
        </div>

        <div class="section">
            <h3>بيانات الطرف الأول (المؤجر)</h3>
            <table>
                <tr>
                    <th>اسم الشركة / النشاط</th>
                    <td>{{ $settings['business_name'] ?? 'بشرى خير' }}</td>
                </tr>
                <tr>
                    <th>العنوان</th>
                    <td>{{ $settings['address'] ?? '—' }}</td>
                </tr>
                <tr>
                    <th>رقم الهاتف</th>
                    <td>{{ $settings['phone'] ?? '—' }}</td>
                </tr>
                <tr>
                    <th>البريد الإلكتروني</th>
                    <td>{{ $settings['email'] ?? '—' }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h3>بيانات الطرف الثاني (المستأجر)</h3>
            <table>
                <tr>
                    <th>الاسم</th>
                    <td>{{ $booking->customer?->name ?? '—' }}</td>
                </tr>
                <tr>
                    <th>رقم الهاتف</th>
                    <td>{{ $booking->customer?->phone ?? '—' }}</td>
                </tr>
                <tr>
                    <th>رقم هاتف بديل</th>
                    <td>{{ $booking->customer?->phone_secondary ?: '—' }}</td>
                </tr>
                <tr>
                    <th>البريد الإلكتروني</th>
                    <td>{{ $booking->customer?->email ?: '—' }}</td>
                </tr>
                <tr>
                    <th>العنوان</th>
                    <td>{{ $booking->customer?->address ?: '—' }}</td>
                </tr>
                <tr>
                    <th>الرقم القومي</th>
                    <td>{{ $booking->customer?->national_id ?: '—' }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h3>بيانات الوحدة العقارية</h3>
            <table>
                <tr>
                    <th>اسم الوحدة</th>
                    <td>{{ $booking->property?->name ?? '—' }}</td>
                </tr>
                <tr>
                    <th>كود الوحدة</th>
                    <td>{{ $booking->property?->code ?? '—' }}</td>
                </tr>
                <tr>
                    <th>النوع</th>
                    <td>{{ $booking->property?->type?->name ?? '—' }}</td>
                </tr>
                <tr>
                    <th>الموقع</th>
                    <td>
                        {{ collect([
                            $booking->property?->address,
                            $booking->property?->district,
                            $booking->property?->city,
                        ])->filter()->implode('، ') ?: '—' }}
                    </td>
                </tr>
                <tr>
                    <th>المساحة / الغرف</th>
                    <td>
                        @if($booking->property?->area) المساحة: {{ $booking->property->area }} م² @endif
                        @if($booking->property?->rooms) — الغرف: {{ $booking->property->rooms }} @endif
                        @if($booking->property?->bathrooms) — الحمامات: {{ $booking->property->bathrooms }} @endif
                        @if(!$booking->property?->area && !$booking->property?->rooms) — @endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h3>مدة الإيجار والقيمة المالية</h3>
            <table>
                <tr>
                    <th>تاريخ البداية</th>
                    <td>{{ $booking->start_date?->format('Y-m-d') }}</td>
                </tr>
                <tr>
                    <th>تاريخ النهاية</th>
                    <td>{{ $booking->end_date?->format('Y-m-d') }}</td>
                </tr>
                <tr>
                    <th>عدد الليالي / الأيام</th>
                    <td>{{ $booking->nights }}</td>
                </tr>
                <tr>
                    <th>سعر الليلة / اليوم</th>
                    <td>{{ number_format((float) $booking->nightly_rate, 2) }} {{ $currency }}</td>
                </tr>
                <tr>
                    <th>قيمة الإيجار</th>
                    <td>{{ number_format((float) $booking->rent_amount, 2) }} {{ $currency }}</td>
                </tr>
                <tr>
                    <th>الخصم</th>
                    <td>
                        {{ number_format((float) $booking->discount, 2) }} {{ $currency }}
                        @if(($booking->discount_type ?? 'amount') === 'percent' && (float) ($booking->discount_value ?? 0) > 0)
                            ({{ rtrim(rtrim(number_format((float) $booking->discount_value, 2), '0'), '.') }}%)
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>المبلغ الإضافي</th>
                    <td>
                        {{ number_format((float) $booking->extra_amount, 2) }} {{ $currency }}
                        @if(($booking->extra_type ?? 'amount') === 'percent' && (float) ($booking->extra_value ?? 0) > 0)
                            ({{ rtrim(rtrim(number_format((float) $booking->extra_value, 2), '0'), '.') }}%)
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>الإجمالي</th>
                    <td><strong>{{ number_format((float) $booking->total, 2) }} {{ $currency }}</strong></td>
                </tr>
                <tr>
                    <th>المدفوع</th>
                    <td>{{ number_format((float) $booking->paid_amount, 2) }} {{ $currency }}</td>
                </tr>
                <tr>
                    <th>المتبقي</th>
                    <td>{{ number_format((float) $booking->remaining_amount, 2) }} {{ $currency }}</td>
                </tr>
                <tr>
                    <th>طريقة الدفع</th>
                    <td>{{ $paymentMethod }}</td>
                </tr>
                <tr>
                    <th>حالة العقد</th>
                    <td>{{ $statusLabel }}</td>
                </tr>
            </table>
        </div>

        @if($booking->payments->isNotEmpty())
            <div class="section">
                <h3>سجل الدفعات</h3>
                <table>
                    <tr>
                        <th>رمز الدفعة</th>
                        <th>المبلغ</th>
                        <th>التاريخ</th>
                        <th>الطريقة</th>
                    </tr>
                    @foreach($booking->payments as $payment)
                        <tr>
                            <td>{{ $payment->code }}</td>
                            <td>{{ number_format((float) $payment->amount, 2) }} {{ $currency }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($payment->paid_at)->format('Y-m-d') }}</td>
                            <td>{{ \App\Support\Labels::paymentMethod($payment->payment_method) }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        <div class="section">
            <h3>الشروط والأحكام</h3>
            <ol class="terms">
                <li>يلتزم المستأجر باستخدام الوحدة للغرض المتفق عليه والمحافظة عليها طوال مدة الإيجار.</li>
                <li>يلتزم المستأجر بسداد القيمة الإيجارية والإضافات المتفق عليها في المواعيد المحددة بهذا العقد.</li>
                <li>لا يجوز للمستأجر التنازل عن الوحدة أو تأجيرها من الباطن دون موافقة كتابية من المؤجر.</li>
                <li>يتحمل المستأجر أي تلفيات ناتجة عن سوء الاستخدام خلال فترة الإيجار.</li>
                <li>يلتزم المستأجر بتسليم الوحدة في نهاية المدة بالحالة التي استلمها عليها.</li>
                <li>في حالة الإلغاء أو الإنهاء المبكر، تطبق سياسات الشركة المعمول بها وقت التعاقد.</li>
                <li>يقر الطرفان بأن جميع البيانات الواردة بالعقد صحيحة، وأن هذا العقد يمثل الاتفاق الكامل بينهما.</li>
                @if($booking->notes)
                    <li>ملاحظات إضافية: {{ $booking->notes }}</li>
                @endif
            </ol>
        </div>

        <div class="signatures">
            <div class="sign-box">
                <strong>توقيع الطرف الأول (المؤجر)</strong>
                الاسم: .........................<br>
                التوقيع: .........................
            </div>
            <div class="sign-box">
                <strong>توقيع الطرف الثاني (المستأجر)</strong>
                الاسم: {{ $booking->customer?->name ?? '.........................' }}<br>
                التوقيع: .........................
            </div>
        </div>

        <div class="footer">
            تم إنشاء هذا العقد تلقائيًا من نظام {{ $settings['business_name'] ?? 'بشرى خير' }} — {{ $printedAt }}
        </div>
    </div>
</body>
</html>

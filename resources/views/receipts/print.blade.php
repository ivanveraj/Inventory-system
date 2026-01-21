<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo #{{ $data['sale_id'] ?? '' }}</title>
    <style>
        @page {
            size: 72mm auto;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 72mm;
            margin: 0 auto;
            padding: 0 4mm 8mm 4mm;
            background: #fff;
            color: #000;
        }

        .logo-container {
            text-align: center;
        }

        .logo {
            width: 100px;
            height: 100px;
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .info {
            margin-bottom: 8px;
        }

        .info p {
            margin: 2px 0;
        }

        .items {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 8px 0;
            margin-bottom: 8px;
        }

        .item {
            display: flex;
            justify-content: space-between;
            margin: 4px 0;
        }

        .item-name {
            flex: 1;
            word-break: break-word;
        }

        .item-qty {
            width: 30px;
            text-align: center;
        }

        .item-total {
            width: 60px;
            text-align: right;
        }

        .total {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            padding-top: 8px;
        }

        .footer {
            text-align: center;
            margin-top: 12px;
            font-size: 16px;
        }

        /* Ocultar debugbar y cualquier cosa inyectada */
        .phpdebugbar,
        #debugbar,
        .sf-toolbar,
        script,
        noscript {
            display: none !important;
        }

        @media print {
            body {
                width: 72mm;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .phpdebugbar,
            #debugbar,
            .sf-toolbar {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="logo-container">
        <img src="{{ asset('img/zona-8-logo.jpg') }}" alt="Logo" class="logo" width="100" height="100">
    </div>
    <div class="header">
        <h2>Venta #{{ $data['sale_random_id'] ?? ($data['history_sale_id'] ?? ($data['sale_id'] ?? '-')) }}</h2>
    </div>

    <div class="info">
        <p><strong>Fecha:</strong> {{ $data['date'] ?? now()->format('d/m/Y H:i') }}</p>
        <p><strong>Cliente:</strong> {{ $data['client'] ?? 'Venta General' }}</p>
        @if (!empty($data['payment_method']))
            <p><strong>Metodo de Pago:</strong> {{ $data['payment_method'] }}</p>
        @endif
    </div>

    @if (!empty($data['time']) && $data['time'] > 0)
        <div class="info" style="border-top: 1px dashed #000; padding-top: 8px;">
            @if (!empty($data['start_time']))
                <p><strong>Inicio:</strong> {{ $data['start_time'] }}</p>
            @endif
            @if (!empty($data['end_time']))
                <p><strong>Fin:</strong> {{ $data['end_time'] }}</p>
            @endif
            <p><strong>Duración:</strong> {{ $data['time'] }} minutos</p>
            <p><strong>Precio Tiempo:</strong> {{ formatMoney($data['price_time'] ?? 0) }}</p>

            @if (!empty($data['min_time_applied']) && $data['min_time_applied'])
                <div style="margin-top: 6px; padding: 4px; border: 1px solid #000; font-size: 10px;">
                    <p style="margin: 0;"><strong>Nota:</strong> Tiempo jugado: {{ $data['real_time'] ?? 0 }} min.</p>
                    <p style="margin: 0;">El tiempo mínimo es {{ $data['min_time_value'] ?? 0 }} min.</p>
                    <p style="margin: 0;">Se cobra el precio mínimo.</p>
                </div>
            @endif
        </div>
    @endif

    @if (!empty($data['items']))
        <div class="items">
            <div class="item" style="font-weight: bold; border-bottom: 1px solid #ccc; padding-bottom: 4px;">
                <span class="item-name">Producto</span>
                <span class="item-qty">Cant</span>
                <span class="item-total">Total</span>
            </div>
            @foreach ($data['items'] ?? [] as $item)
                <div class="item">
                    <span class="item-name">{{ $item['name'] ?? 'Producto' }}</span>
                    <span class="item-qty">{{ $item['amount'] ?? 0 }}</span>
                    <span class="item-total">{{ formatMoney($item['total'] ?? 0) }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <div class="total">
        TOTAL: {{ formatMoney($data['total'] ?? 0) }}
    </div>

    <div class="footer">
        <p>¡Gracias por su compra!</p>
    </div>

    <script>
        document.querySelectorAll('.phpdebugbar, #debugbar, .sf-toolbar').forEach(el => el.remove());

        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 100);

            window.onafterprint = function() {
                window.close();
            };
        };
    </script>
</body>

</html>
